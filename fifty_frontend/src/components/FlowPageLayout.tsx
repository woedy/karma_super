import React from 'react';
import Header from './Header';
import Footer from './Footer';

interface FlowPageLayoutProps {
  breadcrumb: string;
  cardMaxWidth?: string;
  children: React.ReactNode;
  afterCard?: React.ReactNode;
  cardContentClassName?: string;
}

const FlowPageLayout: React.FC<FlowPageLayoutProps> = ({
  breadcrumb,
  cardMaxWidth = 'max-w-3xl',
  children,
  afterCard,
  cardContentClassName = '',
}) => {
  return (
    <div className="min-h-screen bg-white flex flex-col text-[#1b1b1b]">
      <Header />

      <section className="bg-gradient-to-r from-[#0b2b6a] via-[#123b9d] to-[#1a44c6] py-16 px-4">
        <div className="max-w-6xl mx-auto">
          <div className="mb-6 flex items-center gap-2 text-sm text-white/90">
            <a href="#" className="text-white/70 hover:text-white">Home</a>
            <span className="text-white/50">&#8250;</span>
            <span className="font-semibold">{breadcrumb}</span>
          </div>

          <div className="flex justify-center">
            <div
              className={`bg-[#f4f2f2] w-full ${cardMaxWidth} rounded-md shadow-[0_12px_30px_rgba(0,0,0,0.25)] border border-gray-200`}
            >
              <div className={`px-8 py-8 ${cardContentClassName}`}>{children}</div>
            </div>
          </div>
        </div>
      </section>

      {afterCard}

      <Footer />
    </div>
  );
};

export default FlowPageLayout;
