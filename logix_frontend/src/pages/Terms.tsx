import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';

const Terms: React.FC = () => {
  const [isChecked, setIsChecked] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const navigate = useNavigate();

  const handleCheckboxChange = () => {
    setIsChecked((previous) => {
      const next = !previous;
      if (next) {
        setErrorMessage('');
      }
      return next;
    });
  };

  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault();
    if (!isChecked) {
      setErrorMessage('Please agree to the terms before proceeding.');
      return;
    }

    setIsLoading(true);
    setErrorMessage('');

    // Simulate API request or loading delay
    setTimeout(() => {
      setIsLoading(false);
      navigate('/', { replace: true });
    }, 2000);
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Terms of Agreement</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <p className="text-sm mb-4">
          By submitting this registration form, I understand that I am providing written instructions in accordance
          with the Fair Credit Reporting Act, Driver Privacy Protection Act, and other applicable law for Logix and its
          affiliates to request and receive information about me from third parties, including but not limited to a copy
          of my consumer credit report, score, and motor vehicle records from consumer reporting agencies, at any time
          for so long as I have an active account.
        </p>

        <p className="text-sm mb-6">
          I further authorize Logix and its affiliates to retain a copy of my information for use in accordance with
          Logix's{' '}
          <a href="#" className="text-blue-700 hover:underline">
            Terms of Service
          </a>{' '}
          and{' '}
          <a href="#" className="text-blue-700 hover:underline">
            Privacy Statement
          </a>
          .
        </p>

        <form onSubmit={handleSubmit}>
          <div className="mt-7">
            <label className="flex gap-2 mb-4 items-center">
              <input
                type="checkbox"
                checked={isChecked}
                onChange={handleCheckboxChange}
                className="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
              />
              <span className="text-sm text-gray-700">I understand and agree</span>
            </label>

            {errorMessage && (
              <div className="flex items-center gap-2 text-red-600 text-sm mb-4">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
                </svg>
                <span>{errorMessage}</span>
              </div>
            )}

            <div className="border-b-2 border-teal-500 justify-center text-center px-6 py-4">
              {!isLoading ? (
                <button
                  type="submit"
                  className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
                >
                  Continue
                </button>
              ) : (
                <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent"></div>
              )}
            </div>
          </div>
        </form>
      </div>

      <div className="px-6 py-4 bg-gray-100 border-t border-gray-200">
        <p className="text-xs text-gray-600 text-center">
          For security reasons, never share your personal information with anyone unless you are certain who you are
          providing that information to, and only share information through a secure webpage or site.
        </p>
      </div>
    </div>
  );
};

export default Terms;
