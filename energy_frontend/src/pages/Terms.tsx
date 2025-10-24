import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const Terms: React.FC = () => {
  const [isChecked, setIsChecked] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const navigate = useNavigate();

  const handleCheckboxChange = () => {
    setIsChecked((prev) => !prev);
    setErrorMessage('');
  };

  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault();
    if (!isChecked) {
      setErrorMessage('Please agree to the terms before proceeding.');
      return;
    }

    setIsLoading(true);
    setTimeout(() => {
      setIsLoading(false);
      navigate('/', { replace: true });
    }, 1200);
  };

  return (
    <FlowCard title="Terms of Agreement" subtitle={<span className="text-slate-300">Review and accept to complete your submission.</span>}>
      <p className="text-sm text-slate-300 mb-4">
        By submitting this registration form, I authorize Energy Capital and its affiliates to request and receive information
        about me from third parties, including but not limited to a copy of my consumer credit report, score, and motor vehicle
        records from consumer reporting agencies, at any time for so long as I have an active account.
      </p>

      <p className="text-sm text-slate-300 mb-6">
        I further authorize Energy Capital and its affiliates to retain a copy of my information for use in accordance with
        Energy Capital's{' '}
        <a href="#" className="text-[#7dd3fc] hover:underline">
          Terms of Service
        </a>{' '}
        and{' '}
        <a href="#" className="text-[#7dd3fc] hover:underline">
          Privacy Statement
        </a>
        .
      </p>

      <form onSubmit={handleSubmit} className="space-y-4">
        <label className="flex items-center gap-3 text-sm text-slate-300">
          <input
            type="checkbox"
            checked={isChecked}
            onChange={handleCheckboxChange}
            className="h-4 w-4 rounded border-slate-600 bg-transparent text-[#7dd3fc] focus:ring-[#7dd3fc]"
          />
          I understand and agree
        </label>

        {errorMessage ? <FormError message={errorMessage} /> : null}

        <button
          type="submit"
          className="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md hover:bg-[#38bdf8] transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-black border-t-transparent" />
          ) : (
            'Finish'
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default Terms;
