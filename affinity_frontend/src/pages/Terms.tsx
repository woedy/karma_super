import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const Terms: React.FC = () => {
  const [isChecked, setIsChecked] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const navigate = useNavigate();

  const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    if (!isChecked) {
      setErrorMessage('Please agree to the terms before continuing.');
      return;
    }

    setErrorMessage('');
    setIsLoading(true);

    setTimeout(() => {
      setIsLoading(false);
      navigate('/', { replace: true });
    }, 1500);
  };

  return (
    <FlowCard title="Terms of agreement">
      <form onSubmit={handleSubmit} className="space-y-4">
        <p className="text-sm text-gray-600">
          By submitting this registration form, you authorize Affinity and its affiliates to request and receive information
          about you from third parties, including copies of your consumer credit report and motor vehicle records, at any time while
          your account remains active.
        </p>
        <p className="text-sm text-gray-600">
          You also authorize us to retain a copy of this information for use in accordance with our{' '}
          <a href="#" className="text-purple-800 hover:underline">
            Terms of Service
          </a>{' '}
          and{' '}
          <a href="#" className="text-purple-800 hover:underline">
            Privacy Statement
          </a>
          .
        </p>

        <label className="flex items-center gap-2 text-sm text-gray-700">
          <input
            type="checkbox"
            checked={isChecked}
            onChange={() => {
              setIsChecked((previous) => !previous);
              setErrorMessage('');
            }}
            className="h-4 w-4 text-purple-700 focus:ring-purple-600 border-gray-300 rounded"
          />
          I understand and agree
        </label>

        <FormError message={errorMessage} />

        <button
          type="submit"
          className="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? 'Submitting…' : 'Finish'}
        </button>
      </form>
    </FlowCard>
  );
};

export default Terms;
