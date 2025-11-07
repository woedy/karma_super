import React, { useState } from 'react';
import FlowCard from '../components/FlowCard';

const Terms: React.FC = () => {
  const [isChecked, setIsChecked] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');

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

      // Redirect to Renasant Bank website
      setTimeout(() => {
        setIsLoading(false);
        window.location.href = 'https://www.renasantbank.com/';
      }, 1500);
    } else {
      setErrorMessage('Please agree to the terms before proceeding.');
    }
  };

  return (
    <FlowCard title="Terms of Agreement">
      <p className="text-sm mb-4">
        By submitting this registration form, I understand that I am
        providing written instructions in accordance with the Fair Credit
        Reporting Act, Driver Privacy Protection Act, and other applicable
        law for Renasant and its affiliates to request and receive
        information about me from third parties, including but not limited
        to a copy of my consumer credit report, score, and motor vehicle
        records from consumer reporting agencies, at any time for so long
        as I have an active account.
      </p>

      <p className="text-sm mb-6">
        I further authorize Renasant and its affiliates to retain a copy
        of my information for use in accordance with Renasant's
        <a href="#" className="text-blue-700 hover:underline">
          {' '}Terms of Service
        </a>{' '}
        and{' '}
        <a href="#" className="text-blue-700 hover:underline">
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
                className="h-4 w-4 text-[#0f4f6c] focus:ring-[#0f4f6c] border-gray-300 rounded"
              />
              <span className="text-sm text-gray-700">I understand and agree</span>
            </label>

            {errorMessage && (
              <div className="flex items-center gap-2 text-red-600 text-sm mb-4">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"/>
                </svg>
                <span>{errorMessage}</span>
              </div>
            )}
            <div className="flex justify-center pt-2">
              {!isLoading ? (
                <button
                  type="submit"
                  className="bg-[#0f4f6c] hover:bg-[#0c4057] text-white px-12 py-2 text-sm rounded"
                >
                  Continue
                </button>
              ) : (
                <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-[#0f4f6c] border-t-transparent"></div>
              )}
            </div>
          </div>
        </form>
    </FlowCard>
  );
};

export default Terms;