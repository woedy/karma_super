import { useEffect } from 'react';

interface FlowHelmetProps {
  title: string;
}

const FlowHelmet: React.FC<FlowHelmetProps> = ({ title }) => {
  useEffect(() => {
    const previousTitle = document.title;
    document.title = title ? `${title} · Bluegrass Community FCU` : 'Bluegrass Community FCU';
    return () => {
      document.title = previousTitle;
    };
  }, [title]);

  return null;
};

export default FlowHelmet;
