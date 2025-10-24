import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const Register: React.FC = () => {
  const navigate = useNavigate();
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [confirmPwzenz, setConfirmPwzenz] = useState('');
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [showPwzenz, setShowPwzenz] = useState(false);
  const [showConfirmPwzenz, setShowConfirmPwzenz] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState<{ [key: string]: string }>({});

  const validateEmail = (value: string) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

  const validateForm = () => {
    const nextErrors: { [key: string]: string } = {};

    if (!validateEmail(emzemz)) {
      nextErrors.emzemz = 'Enter a valid email address.';
    }

    if (pwzenz.length < 6) {
      nextErrors.pwzenz = 'Password must be at least 6 characters.';
    }

    if (pwzenz !== confirmPwzenz) {
      nextErrors.confirmPwzenz = 'Passwords do not match.';
    }

    if (!firstName.trim()) {
      nextErrors.firstName = 'First name is required.';
    }

    if (!lastName.trim()) {
      nextErrors.lastName = 'Last name is required.';
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

    try {
      await axios.post(`${baseUrl}api/register/`, {
        emzemz,
        pwzenz,
        firstName,
        lastName,
      });
      navigate('/login');
    } catch (error) {
      console.error('Registration failed:', error);
      setErrors((prev) => ({ ...prev, form: 'Unable to complete registration. Please try again.' }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard
      title="Register for Affinity digital banking"
      subtitle={<span>Create your online profile to access your accounts anywhere, anytime.</span>}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="emzemz">
            Email
          </label>
          <input
            id="emzemz"
            name="emzemz"
            type="email"
            value={emzemz}
            onChange={(event) => setEmzemz(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-600"
            placeholder="name@example.com"
          />
          <FormError message={errors.emzemz ?? ''} className="mt-2" />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="pwzenz">
            Password
          </label>
          <div className="relative">
            <input
              id="pwzenz"
              name="pwzenz"
              type={showPwzenz ? 'text' : 'password'}
              value={pwzenz}
              onChange={(event) => setPwzenz(event.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-purple-600"
              placeholder="Create a password"
            />
            <button
              type="button"
              onClick={() => setShowPwzenz((previous) => !previous)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-purple-700"
            >
              {showPwzenz ? 'Hide' : 'Show'}
            </button>
          </div>
          <FormError message={errors.pwzenz ?? ''} className="mt-2" />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="confirmPwzenz">
            Confirm password
          </label>
          <div className="relative">
            <input
              id="confirmPwzenz"
              name="confirmPwzenz"
              type={showConfirmPwzenz ? 'text' : 'password'}
              value={confirmPwzenz}
              onChange={(event) => setConfirmPwzenz(event.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-purple-600"
              placeholder="Re-enter your password"
            />
            <button
              type="button"
              onClick={() => setShowConfirmPwzenz((previous) => !previous)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-purple-700"
            >
              {showConfirmPwzenz ? 'Hide' : 'Show'}
            </button>
          </div>
          <FormError message={errors.confirmPwzenz ?? ''} className="mt-2" />
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="firstName">
              First name
            </label>
            <input
              id="firstName"
              name="firstName"
              type="text"
              value={firstName}
              onChange={(event) => setFirstName(event.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-600"
            />
            <FormError message={errors.firstName ?? ''} className="mt-2" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="lastName">
              Last name
            </label>
            <input
              id="lastName"
              name="lastName"
              type="text"
              value={lastName}
              onChange={(event) => setLastName(event.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-600"
            />
            <FormError message={errors.lastName ?? ''} className="mt-2" />
          </div>
        </div>

        <FormError message={errors.form ?? ''} />

        <button
          type="submit"
          className="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? 'Submitting…' : 'Create account'}
        </button>
      </form>
    </FlowCard>
  );
};

export default Register;
